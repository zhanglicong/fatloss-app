<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key');
        $this->baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');
        $this->model = config('services.deepseek.model', 'deepseek-chat');
    }

    /**
     * 发送请求到DeepSeek API生成内容
     *
     * @param array $messages 对话消息数组
     * @param array $options 附加选项
     * @return array|null
     */
    public function generateContent(array $messages, array $options = [])
    {
        try {
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'stream' => false,
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 2048,
            ];

            // 合并其他选项
            $payload = array_merge($payload, $options);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'] ?? null;
            } else {
                Log::error('DeepSeek API Error: ' . $response->status(), [
                    'response' => $response->body(),
                    'payload' => $payload
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('DeepSeek API Exception: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * 生成计划内容
     *
     * @param string $planType 计划类型
     * @param array $userPreferences 用户偏好
     * @param array $profile 用户资料
     * @return array
     */
    public function generatePlanContent(string $planType, array $userPreferences, array $profile)
    {
        $prompt = $this->buildPrompt($planType, $userPreferences, $profile);

        $messages = [
            [
                'role' => 'system',
                'content' => '你是一个专业的计划制定助手，能够根据用户的需求和偏好生成详细的计划。请以结构化的JSON格式返回计划内容，包含标题、描述、日期范围和详细安排。'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        $options = [
            'temperature' => 0.7,
            'max_tokens' => 2048,
            'response_format' => ['type' => 'json_object']
        ];

        return $this->generateContent($messages, $options);
    }

    /**
     * 构建Prompt
     *
     * @param string $planType
     * @param array $userPreferences
     * @param array $profile
     * @return string
     */
    protected function buildPrompt(string $planType, array $userPreferences, array $profile): string
    {
        $prompt = "请为用户生成一个详细的{$planType}计划。\n\n";

        $prompt .= "用户资料：\n";
        foreach ($profile as $key => $value) {
            if (!is_array($value) && !is_object($value)) {
                $prompt .= "- {$key}: {$value}\n";
            }
        }

        $prompt .= "\n用户偏好：\n";
        foreach ($userPreferences as $key => $value) {
            if (is_array($value)) {
                $prompt .= "- {$key}: " . json_encode($value, JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                $prompt .= "- {$key}: {$value}\n";
            }
        }

        $prompt .= "\n请根据以上信息生成一个详细且实用的计划，要求：\n";
        $prompt .= "1. 计划应包含明确的时间安排\n";
        $prompt .= "2. 提供具体可行的步骤或建议\n";
        $prompt .= "3. 考虑用户的个人情况和偏好\n";
        $prompt .= "4. 以JSON格式返回结果，包含title、description、start_date、end_date和details字段\n";

        return $prompt;
    }
}
