<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    // 認證已在路由中處理，無需在 constructor 中重複設定
    
    public function users()
    {
        // 簡單的權限檢查（實際應用中應該使用更完善的權限系統）
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $users = User::with('organization')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($users);
    }
    
    public function organizations()
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $organizations = Organization::withCount('users')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($organizations);
    }
    
    public function systemStats()
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        return response()->json([
            'total_users' => User::count(),
            'total_organizations' => Organization::count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'organizations_this_month' => Organization::whereMonth('created_at', now()->month)->count(),
        ]);
    }
    
    public function createUser(Request $request)
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
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

        return response()->json([
            'message' => '使用者建立成功',
            'user' => $user->load('organization'),
        ], 201);
    }
    
    public function updateUser(Request $request, User $user)
    {
        if (request()->user()->email !== 'admin@purpledesk.com') {
            abort(403, '無權限存取');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        $user->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'email' => $request->email,
            'organization_id' => $request->organization_id,
        ]);

        return response()->json([
            'message' => '使用者更新成功',
            'user' => $user->load('organization'),
        ]);
    }
}
