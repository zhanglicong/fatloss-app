<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'height_cm',
        'weight_kg',
        'age',
        'gender',
        'activity_level',
        'target_weight_kg',
        'target_date',
        'preferences' // 用户偏好设置，用于AI计划生成
    ];
    
    protected $casts = [
        'preferences' => 'array'
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
}