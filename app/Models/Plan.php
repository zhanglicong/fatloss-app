<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type', // 计划类型（例如：学习、旅行、项目管理等）
        'start_date',
        'end_date',
        'details', // 计划详细内容(JSON格式)
        'status', // 计划状态（待办、进行中、已完成、已取消）
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * 获取计划所属用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}