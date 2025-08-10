<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'display_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'organization_id' => $request->organization_id,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->load('organizations', 'teams'),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'message' => '登入資訊無效',
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->load('organizations', 'teams'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => '登出成功',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('organizations', 'teams'),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $rules = [
            'name' => 'nullable|string|max:255',
            'display_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
        
        // 如果有提供目前密碼，則驗證密碼相關欄位
        if ($request->filled('current_password')) {
            $rules['current_password'] = 'required';
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $request->validate($rules);
        
        // 如果要更改密碼，先驗證目前密碼
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => '目前密碼不正確',
                ]);
            }
        }
        
        $data = [
            'name' => $request->name,
            'display_name' => $request->display_name,
            'email' => $request->email,
        ];
        
        // 更新密碼
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        // 處理頭像上傳
        if ($request->hasFile('avatar')) {
            // 刪除舊頭像
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $data['avatar'] = $request->file('avatar')->store('avatars/users', 'public');
        }
        
        $user->update($data);
        
        return response()->json([
            'message' => '個人資料已成功更新',
            'user' => $user->load('organizations', 'teams'),
        ]);
    }
    
    public function updateSettings(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'locale' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'email_notifications' => 'nullable|boolean',
            'browser_notifications' => 'nullable|boolean',
            'theme' => 'nullable|string|in:light,dark,auto',
        ]);
        
        $user->update([
            'locale' => $request->locale ?? $user->locale,
            'timezone' => $request->timezone ?? $user->timezone,
            'email_notifications' => $request->email_notifications ?? $user->email_notifications,
            'browser_notifications' => $request->browser_notifications ?? $user->browser_notifications,
            'theme' => $request->theme ?? $user->theme,
        ]);
        
        return response()->json([
            'message' => '設定已成功更新',
            'user' => $user->load('organizations', 'teams'),
        ]);
    }
}
