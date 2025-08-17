<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icon Picker 測試頁面</title>
    @vite(['resources/css/app.css', 'resources/js/test-page.js'])
    <style>
        .test-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }
        .test-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            max-width: 1200px;
            margin: 0 auto;
        }
        .test-header {
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .test-content {
            padding: 2rem;
        }
        .icon-preview {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }
        .trigger-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .trigger-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        .phase-progress {
            background: #f1f5f9;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .progress-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .progress-item:last-child {
            margin-bottom: 0;
        }
        .status-completed {
            color: #22c55e;
        }
        .status-current {
            color: #f59e0b;
        }
        .status-pending {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-card">
            <div class="test-header">
                <h1 class="text-3xl font-bold mb-2">Icon Picker 測試頁面</h1>
                <p class="text-xl opacity-90">測試 Icon Picker 重構進度與功能</p>
            </div>
            
            <div class="test-content">
                <div id="app">
                    <icon-picker-test-page></icon-picker-test-page>
                </div>
            </div>
        </div>
    </div>
</body>
</html>