<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DietPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DietPlanController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'meals' => 'nullable|array',
            'total_calories' => 'nullable|integer',
            'protein' => 'nullable|integer',
            'carbs' => 'nullable|integer',
            'fat' => 'nullable|integer',
        ]);

        $data['user_id'] = $request->user()->id;
        $plan = DietPlan::create($data);

        return response()->json(['message' => '饮食计划创建成功', 'plan' => $plan]);
    }

    public function index(Request $request): JsonResponse
    {
        $plans = DietPlan::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($plans);
    }

    public function show($id): JsonResponse
    {
        $plan = DietPlan::findOrFail($id);
        return response()->json($plan);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $plan = DietPlan::findOrFail($id);
        if ($plan->user_id !== $request->user()->id) {
            return response()->json(['message' => '无权操作'], 403);
        }

        $plan->update($request->all());
        return response()->json(['message' => '更新成功', 'plan' => $plan]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $plan = DietPlan::findOrFail($id);
        if ($plan->user_id !== $request->user()->id) {
            return response()->json(['message' => '无权操作'], 403);
        }

        $plan->delete();
        return response()->json(['message' => '删除成功']);
    }
}
