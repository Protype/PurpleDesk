<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BootstrapIconService;
use Illuminate\Http\JsonResponse;

class BootstrapIconController extends Controller
{
    private BootstrapIconService $bootstrapIconService;
    
    public function __construct(BootstrapIconService $bootstrapIconService)
    {
        $this->bootstrapIconService = $bootstrapIconService;
    }
    
    /**
     * 取得所有 Bootstrap Icons 資料
     */
    public function index(): JsonResponse
    {
        return response()->json($this->bootstrapIconService->getAllBootstrapIcons());
    }
    
    /**
     * 取得分類清單
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => $this->bootstrapIconService->getCategories()
        ]);
    }
    
    /**
     * 取得指定分類的 Bootstrap Icons
     */
    public function category(string $category): JsonResponse
    {
        try {
            $result = $this->bootstrapIconService->getBootstrapIconsByCategory($category);
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}