<?php
namespace App\Services;

use App\Models\Plan;
use Carbon\Carbon;

class PlanService
{
    /**
     * 生成通用AI计划
     *
     * @param \App\Models\User $user 用户对象
     * @param string $type 计划类型
     * @param array $preferences 用户偏好设置
     * @return array 生成的计划
     */
    public function generatePlan($user, $type, $preferences = []): array
    {
        // 根据不同类型生成不同的计划
        switch ($type) {
            case 'study':
                return $this->generateStudyPlan($user, $preferences);
            case 'travel':
                return $this->generateTravelPlan($user, $preferences);
            case 'project':
                return $this->generateProjectPlan($user, $preferences);
            case 'habit':
                return $this->generateHabitPlan($user, $preferences);
            default:
                return $this->generateGenericPlan($user, $type, $preferences);
        }
    }

    /**
     * 生成学习计划
     */
    protected function generateStudyPlan($user, $preferences): array
    {
        $topics = $preferences['topics'] ?? [];
        $duration = $preferences['duration'] ?? 7; // 默认7天
        $hoursPerDay = $preferences['hours_per_day'] ?? 2;

        $planDetails = [
            'type' => 'study',
            'topics' => $topics,
            'duration_days' => $duration,
            'hours_per_day' => $hoursPerDay,
            'daily_schedule' => []
        ];

        // 生成每日计划
        for ($i = 0; $i < $duration; $i++) {
            $day = $i + 1;
            $planDetails['daily_schedule'][] = [
                'day' => $day,
                'date' => Carbon::today()->addDays($i)->format('Y-m-d'),
                'topics' => $this->assignTopicsForDay($topics, $day, $duration),
                'estimated_hours' => $hoursPerDay
            ];
        }

        return [
            'title' => '学习计划',
            'description' => '为您量身定制的学习计划',
            'type' => 'study',
            'start_date' => Carbon::today()->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays($duration - 1)->format('Y-m-d'),
            'details' => $planDetails
        ];
    }

    /**
     * 生成旅行计划
     */
    protected function generateTravelPlan($user, $preferences): array
    {
        $destination = $preferences['destination'] ?? '未指定目的地';
        $duration = $preferences['duration'] ?? 3;
        
        $planDetails = [
            'type' => 'travel',
            'destination' => $destination,
            'duration_days' => $duration,
            'itinerary' => []
        ];

        // 生成每日行程
        for ($i = 0; $i < $duration; $i++) {
            $day = $i + 1;
            $planDetails['itinerary'][] = [
                'day' => $day,
                'date' => Carbon::today()->addDays($i)->format('Y-m-d'),
                'activities' => $this->suggestDailyActivities($destination, $day),
                'accommodation' => $day < $duration ? '酒店' : '返程'
            ];
        }

        return [
            'title' => '旅行计划: ' . $destination,
            'description' => '前往 ' . $destination . ' 的旅行计划',
            'type' => 'travel',
            'start_date' => Carbon::today()->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays($duration - 1)->format('Y-m-d'),
            'details' => $planDetails
        ];
    }

    /**
     * 生成项目计划
     */
    protected function generateProjectPlan($user, $preferences): array
    {
        $projectName = $preferences['project_name'] ?? '未命名项目';
        $deadline = $preferences['deadline'] ?? Carbon::today()->addWeeks(2)->format('Y-m-d');
        
        $startDate = Carbon::today();
        $endDate = Carbon::parse($deadline);
        $durationDays = $startDate->diffInDays($endDate) + 1;

        $planDetails = [
            'type' => 'project',
            'project_name' => $projectName,
            'deadline' => $deadline,
            'phases' => []
        ];

        // 定义典型项目阶段
        $phases = [
            ['name' => '项目启动', 'duration_percent' => 10],
            ['name' => '需求分析', 'duration_percent' => 15],
            ['name' => '设计阶段', 'duration_percent' => 20],
            ['name' => '开发实施', 'duration_percent' => 35],
            ['name' => '测试验收', 'duration_percent' => 15],
            ['name' => '项目交付', 'duration_percent' => 5]
        ];

        $usedDays = 0;
        foreach ($phases as $phase) {
            $phaseDuration = max(1, round($durationDays * $phase['duration_percent'] / 100));
            if ($usedDays + $phaseDuration > $durationDays) {
                $phaseDuration = $durationDays - $usedDays;
            }
            
            $phaseStart = $startDate->copy()->addDays($usedDays);
            $phaseEnd = $phaseStart->copy()->addDays($phaseDuration - 1);
            
            $planDetails['phases'][] = [
                'name' => $phase['name'],
                'start_date' => $phaseStart->format('Y-m-d'),
                'end_date' => $phaseEnd->format('Y-m-d'),
                'duration_days' => $phaseDuration,
                'tasks' => $this->generatePhaseTasks($phase['name'])
            ];
            
            $usedDays += $phaseDuration;
            if ($usedDays >= $durationDays) break;
        }

        return [
            'title' => '项目计划: ' . $projectName,
            'description' => '项目「' . $projectName . '」的执行计划',
            'type' => 'project',
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'details' => $planDetails
        ];
    }

    /**
     * 生成习惯养成计划
     */
    protected function generateHabitPlan($user, $preferences): array
    {
        $habits = $preferences['habits'] ?? [];
        $duration = $preferences['duration'] ?? 21; // 默认21天习惯养成
        
        $planDetails = [
            'type' => 'habit',
            'habits' => $habits,
            'duration_days' => $duration,
            'tracking' => []
        ];

        // 生成每日跟踪表
        for ($i = 0; $i < $duration; $i++) {
            $day = $i + 1;
            $planDetails['tracking'][] = [
                'day' => $day,
                'date' => Carbon::today()->addDays($i)->format('Y-m-d'),
                'habits_status' => array_fill_keys($habits, false)
            ];
        }

        return [
            'title' => '习惯养成计划',
            'description' => '帮助您养成良好习惯的21天计划',
            'type' => 'habit',
            'start_date' => Carbon::today()->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays($duration - 1)->format('Y-m-d'),
            'details' => $planDetails
        ];
    }

    /**
     * 生成通用计划
     */
    protected function generateGenericPlan($user, $type, $preferences): array
    {
        $duration = $preferences['duration'] ?? 7;
        
        $planDetails = [
            'type' => $type,
            'duration_days' => $duration,
            'custom_fields' => $preferences
        ];

        return [
            'title' => $type . '计划',
            'description' => '为您生成的' . $type . '计划',
            'type' => $type,
            'start_date' => Carbon::today()->format('Y-m-d'),
            'end_date' => Carbon::today()->addDays($duration - 1)->format('Y-m-d'),
            'details' => $planDetails
        ];
    }

    /**
     * 为每天分配学习主题
     */
    private function assignTopicsForDay($topics, $day, $totalDays): array
    {
        if (empty($topics)) return [];
        
        // 简单循环分配主题
        $topicsPerDay = max(1, ceil(count($topics) / $totalDays));
        $startIndex = (($day - 1) * $topicsPerDay) % count($topics);
        
        $assignedTopics = [];
        for ($i = 0; $i < $topicsPerDay && $i < count($topics); $i++) {
            $index = ($startIndex + $i) % count($topics);
            $assignedTopics[] = $topics[$index];
        }
        
        return $assignedTopics;
    }

    /**
     * 为旅行每日推荐活动
     */
    private function suggestDailyActivities($destination, $day): array
    {
        // 这里应该连接到实际的AI服务或数据库获取真实推荐
        $sampleActivities = [
            '景点参观', '当地美食体验', '文化探索', '购物', '休闲娱乐', '户外活动'
        ];
        
        // 根据天数选择不同类型的活动
        $dayActivities = [];
        $activityCount = rand(2, 4);
        
        for ($i = 0; $i < $activityCount; $i++) {
            $dayActivities[] = $sampleActivities[array_rand($sampleActivities)];
        }
        
        return array_unique($dayActivities);
    }

    /**
     * 生成项目阶段任务
     */
    private function generatePhaseTasks($phaseName): array
    {
        // 根据阶段名称生成相应任务
        $taskTemplates = [
            '项目启动' => ['确定项目范围', '组建团队', '召开启动会议'],
            '需求分析' => ['收集用户需求', '编写需求文档', '需求评审'],
            '设计阶段' => ['系统架构设计', '数据库设计', '界面设计'],
            '开发实施' => ['环境搭建', '模块开发', '代码审查', '单元测试'],
            '测试验收' => ['测试用例编写', '系统测试', '用户验收'],
            '项目交付' => ['部署上线', '用户培训', '项目总结']
        ];
        
        return $taskTemplates[$phaseName] ?? ['常规任务1', '常规任务2', '常规任务3'];
    }

    /**
     * 保存计划到数据库
     */
    public function savePlan($user, $planData): Plan
    {
        return Plan::create([
            'user_id' => $user->id,
            'title' => $planData['title'],
            'description' => $planData['description'],
            'type' => $planData['type'],
            'start_date' => $planData['start_date'],
            'end_date' => $planData['end_date'],
            'details' => $planData['details'],
            'status' => 'pending'
        ]);
    }
}