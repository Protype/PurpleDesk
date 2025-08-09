<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => '原型科技有限公司',
                'description' => '專注於網站與應用程式開發的科技公司',
            ],
            [
                'name' => '紫色設計工作室',
                'description' => 'UI/UX 設計與品牌形象設計工作室',
            ],
            [
                'name' => '數位創新團隊',
                'description' => '提供數位轉型與創新解決方案',
            ],
        ];

        foreach ($organizations as $orgData) {
            Organization::create($orgData);
        }
    }
}
