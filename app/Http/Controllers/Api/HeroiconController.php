<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HeroiconService;

class HeroiconController extends Controller
{
    private $heroiconService;
    
    public function __construct(HeroiconService $heroiconService)
    {
        $this->heroiconService = $heroiconService;
    }
    
    /**
     * 取得所有 heroicon 資料
     */
    public function all()
    {
        return response()->json($this->heroiconService->getAllHeroicons());
    }
}