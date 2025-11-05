<?php
namespace App\Services;

use Carbon\Carbon;
use app\Models\WorkoutPlan;
use app\Models\Profile;

class PlanService
{
    // 生成某日的基础计划（非常基础示例）
    public function generateDailyPlans($user): array
    {
        $profile = $user->profile;
        $today = Carbon::today()->toDateString();

        // 计算基础代谢率（简化公式）
        $bmr = $this->estimateBMR($profile);

        // 目标热量（减脂略低于维持）
        $targetCalories = round($bmr * $this->activityFactor($profile->activity_level) - 500);

        $workout = [
            'title'=>'轻度全身训练',
            'duration_min'=>30,
            'exercises'=>[
                ['name'=>'深蹲', 'sets'=>3,'reps'=>'12'],
                ['name'=>'俯卧撑', 'sets'=>3,'reps'=>'10'],
                ['name'=>'平板支撑', 'sets'=>3,'time_s'=>45],
            ]
        ];

        $meal = [
            'title'=>'高蛋白低脂三餐建议',
            'calories'=>$targetCalories,
            'meals'=>[
                ['name'=>'早餐','suggest'=>'燕麦+鸡蛋+水果'],
                ['name'=>'午餐','suggest'=>'鸡胸肉+蔬菜+糙米'],
                ['name'=>'晚餐','suggest'=>'鱼/豆腐+蔬菜'],
            ]
        ];

        // 保存到 plans 表（覆盖当日）
        WorkoutPlan::updateOrCreate(
            ['user_id'=>$user->id,'plan_type'=>'workout','date'=>$today],
            ['content'=>$workout]
        );

        WorkoutPlan::updateOrCreate(
            ['user_id'=>$user->id,'plan_type'=>'meal','date'=>$today],
            ['content'=>$meal]
        );

        return ['workout'=>$workout,'meal'=>$meal];
    }

    protected function estimateBMR(Profile $p)
    {
        // Mifflin-St Jeor 简化
        if($p->gender === 'male'){
            return 10*$p->weight_kg + 6.25*$p->height_cm - 5*$p->age + 5;
        } else {
            return 10*$p->weight_kg + 6.25*$p->height_cm - 5*$p->age - 161;
        }
    }

    protected function activityFactor($level)
    {
        return [
            'sedentary'=>1.2,
            'light'=>1.375,
            'moderate'=>1.55,
            'active'=>1.725,
            'very_active'=>1.9
        ][$level] ?? 1.375;
    }
}
