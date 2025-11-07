<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    /**
     * 创建计划
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'details' => 'nullable|array',
        ]);

        $data['user_id'] = $request->user()->id;
        $plan = Plan::create($data);

        return response()->json(['message' => '计划创建成功', 'plan' => $plan]);
    }

    /**
     * 获取我的计划列表
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');
        
        $query = Plan::where('user_id', $request->user()->id);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $plans = $query->orderBy('created_at', 'desc')->get();

        return response()->json($plans);
    }

    /**
     * 显示计划详情
     */
    public function show($id): JsonResponse
    {
        $plan = Plan::findOrFail($id);
        
        if ($plan->user_id !== request()->user()->id) {
            return response()->json(['message' => '无权访问'], 403);
        }

        return response()->json($plan);
    }

    /**
     * 更新计划
     */
    public function update(Request $request, $id): JsonResponse
    {
        $plan = Plan::findOrFail($id);
        
        if ($plan->user_id !== $request->user()->id) {
            return response()->json(['message' => '无权操作'], 403);
        }

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'details' => 'nullable|array',
            'status' => 'sometimes|string|in:pending,in_progress,completed,cancelled',
        ]);

        $plan->update($data);
        return response()->json(['message' => '更新成功', 'plan' => $plan]);
    }

    /**
     * 删除计划
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $plan = Plan::findOrFail($id);
        
        if ($plan->user_id !== $request->user()->id) {
            return response()->json(['message' => '无权操作'], 403);
        }

        $plan->delete();
        return response()->json(['message' => '删除成功']);
    }

    /**
     * 生成AI计划
     */
    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string',
            'preferences' => 'nullable|array',
        ]);

        $user = $request->user();
        $type = $data['type'];
        $preferences = $data['preferences'] ?? [];

        // 生成计划
        $planData = $this->planService->generatePlan($user, $type, $preferences);

        // 保存计划
        $plan = $this->planService->savePlan($user, $planData);

        return response()->json([
            'message' => 'AI计划生成成功',
            'plan' => $plan
        ]);
    }
}