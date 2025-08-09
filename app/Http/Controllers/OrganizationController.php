<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with('users')->get();
        return response()->json($organizations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/organizations', 'public');
        }

        $organization = Organization::create([
            'name' => $request->name,
            'description' => $request->description,
            'avatar' => $avatarPath,
        ]);

        return response()->json($organization->load('users'), 201);
    }

    public function show(Organization $organization)
    {
        return response()->json($organization->load('users'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('avatar')) {
            // 刪除舊頭像
            if ($organization->avatar) {
                Storage::disk('public')->delete($organization->avatar);
            }
            
            $data['avatar'] = $request->file('avatar')->store('avatars/organizations', 'public');
        }

        $organization->update($data);

        return response()->json($organization->load('users'));
    }

    public function destroy(Organization $organization)
    {
        // 檢查是否有關聯的使用者
        if ($organization->users()->count() > 0) {
            return response()->json([
                'message' => '無法刪除有成員的單位'
            ], 422);
        }

        // 刪除頭像檔案
        if ($organization->avatar) {
            Storage::disk('public')->delete($organization->avatar);
        }

        $organization->delete();

        return response()->json([
            'message' => '單位已成功刪除'
        ]);
    }
}
