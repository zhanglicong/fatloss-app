<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 用户注册
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 自动生成 token（如果你使用 Laravel Sanctum）
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '注册成功',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    /**
     * 用户登录
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => '邮箱或密码错误'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '登录成功',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    /**
     * 获取当前登录用户信息
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * 更新个人信息（身高、体重、目标体重）
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'target_weight' => 'nullable|numeric',
        ]);

        $user = $request->user();
        $user->update($request->only('height', 'weight', 'target_weight'));

        return response()->json([
            'message' => '更新成功',
            'user' => $user
        ]);
    }

    /**
     * 退出登录
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => '退出成功']);
    }
}
