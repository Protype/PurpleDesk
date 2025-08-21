<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Icons 轉換器 - Debug 檢視</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .icon-preview { font-size: 24px; margin-right: 8px; }
        .variant-badge { font-size: 0.7rem; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 0.9rem; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="bi bi-bug"></i> Bootstrap Icons 轉換器 - Debug 檢視</h3>
                    </div>
                    <div class="card-body">
                        
                        <!-- 基本統計 -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4>📊 基本統計</h4>
                                <table class="table table-sm">
                                    @foreach($stats as $key => $value)
                                    <tr>
                                        <td><strong>{{ $key }}:</strong></td>
                                        <td>{{ $value }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>🎨 變體統計</h4>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>僅 Outline:</strong></td>
                                        <td><span class="badge bg-info">{{ $variantStats['outline_only'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>僅 Solid:</strong></td>
                                        <td><span class="badge bg-warning">{{ $variantStats['solid_only'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>雙變體:</strong></td>
                                        <td><span class="badge bg-success">{{ $variantStats['both_variants'] }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- 範例圖標展示 -->
                        <div class="mb-4">
                            <h4>🎯 範例圖標 (前 10 個)</h4>
                            <div class="row">
                                @foreach($sampleIcons as $icon)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi {{ $icon['class'] }} icon-preview"></i>
                                                <div>
                                                    <h6 class="mb-1">{{ $icon['displayName'] }}</h6>
                                                    <small class="text-muted">{{ $icon['name'] }}</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <small class="d-block"><strong>Class:</strong> {{ $icon['class'] }}</small>
                                                <small class="d-block"><strong>Keywords:</strong> {{ implode(', ', $icon['keywords']) }}</small>
                                            </div>
                                            
                                            <div>
                                                @foreach($icon['variants'] as $variant => $data)
                                                    <span class="badge variant-badge {{ $variant === 'outline' ? 'bg-secondary' : 'bg-dark' }} me-1">
                                                        {{ $variant }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 完整資料展示 -->
                        <div class="mb-4">
                            <h4>🗂️ 完整配置資料</h4>
                            <div class="accordion" id="dataAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rawData">
                                            原始 PHP 陣列資料 ({{ count($allIcons) }} 個圖標)
                                        </button>
                                    </h2>
                                    <div id="rawData" class="accordion-collapse collapse" data-bs-parent="#dataAccordion">
                                        <div class="accordion-body">
                                            <div class="code-block">
                                                <pre style="max-height: 400px; overflow-y: scroll;">{{ print_r($allIcons, true) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#jsonData">
                                            JSON 格式資料
                                        </button>
                                    </h2>
                                    <div id="jsonData" class="accordion-collapse collapse" data-bs-parent="#dataAccordion">
                                        <div class="accordion-body">
                                            <div class="code-block">
                                                <pre style="max-height: 400px; overflow-y: scroll;">{{ json_encode($allIcons, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- API 測試 -->
                        <div class="mb-4">
                            <h4>🔗 API 測試連結</h4>
                            <div class="d-flex gap-2">
                                <a href="/api/icons/bootstrap/general" class="btn btn-outline-primary" target="_blank">
                                    <i class="bi bi-link-45deg"></i> 測試 General API
                                </a>
                                <button class="btn btn-outline-secondary" onclick="location.reload()">
                                    <i class="bi bi-arrow-clockwise"></i> 重新整理
                                </button>
                            </div>
                        </div>

                        <!-- 檔案資訊 -->
                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle"></i> 檔案資訊</h5>
                            <p><strong>配置檔案:</strong> <code>config/icon/bootstrap-icons/general_test.php</code></p>
                            <p><strong>轉換來源:</strong> 官方 Bootstrap Icons JSON ({{ count($allIcons) }} 個圖標)</p>
                            <p><strong>生成時間:</strong> {{ date('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>