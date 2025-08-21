<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmojiService;
use Illuminate\Http\JsonResponse;

class EmojiController extends Controller
{
    private EmojiService $emojiService;
    
    public function __construct(EmojiService $emojiService)
    {
        $this->emojiService = $emojiService;
    }
    
    /**
     * 取得所有 emoji 資料
     */
    public function index(): JsonResponse
    {
        return response()->json($this->emojiService->getAllEmojis());
    }
    
    /**
     * 取得分類清單
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => $this->emojiService->getCategories()
        ]);
    }
    
    /**
     * 取得指定分類的 emoji
     */
    public function category(string $category): JsonResponse
    {
        try {
            $result = $this->emojiService->getEmojisByCategory($category);
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
}