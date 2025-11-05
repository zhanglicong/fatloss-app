<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutPlan;
use Illuminate\Http\Request;

class WorkoutPlanController extends Controller
{
    // 创建健身计划
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_muscle' => 'nullable|string',
            'duration' => 'nullable|integer',
            'schedule' => 'nullable|array',
        ]);

        $data['user_id'] = $request->user()->id;
        $plan = WorkoutPlan::create($data);

        return response()->json([
            'message' => '健身计划创建成功',
            'plan' => $plan
        ]);
    }

    // 获取我的健身计划
    public function index(Request $request)
    {
        $plans = WorkoutPlan::where('user_id', $request->user()->id)->get();
        return response()->json($plans);
    }

    // 查看单个计划详情
    public function show($id)
    {
        $plan = WorkoutPlan::findOrFail($id);
        return response()->json($plan);
    }

    // 更新健身计划
    public function update(Request $request, $id)
    {
        $plan = WorkoutPlan::findOrFail($id);

        if ($plan->user_id !== $request->user()->id) {
            return response()->json(['message' => '无权操作'], 403);
        }

        $plan->update($request->all());
        return response()->json(['message' => '更新成功', 'plan' => $plan]);
    }

    // 删除健身计划
    public function destroy(Request $request, $id)
    {
        $plan = WorkoutPlan::findOrFail($id);
        if ($plan->user_id !== $request->user()->id) {
            return response()->json(['message' => '无权操作'], 403);
        }

        $plan->delete();
        return response()->json(['message' => '删除成功']);
    }
}
