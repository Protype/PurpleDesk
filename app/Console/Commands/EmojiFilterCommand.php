<?php

namespace App\Console\Commands;

use App\Services\EmojiFilterService;
use Illuminate\Console\Command;

class EmojiFilterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emoji:filter 
                           {action : Action to perform (status|stats|config)}
                           {--enable : Enable emoji filtering}
                           {--disable : Disable emoji filtering}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage emoji filtering settings and view statistics';

    /**
     * Execute the console command.
     */
    public function handle(EmojiFilterService $filterService)
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'status':
                $this->showStatus($filterService);
                break;
            case 'stats':
                $this->showStats($filterService);
                break;
            case 'config':
                $this->showConfig($filterService);
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: status, stats, config");
                return 1;
        }

        // 處理啟用/停用選項
        if ($this->option('enable') && $this->option('disable')) {
            $this->error('Cannot use both --enable and --disable options together');
            return 1;
        }

        if ($this->option('enable')) {
            $this->info('Note: To enable filtering, set EMOJI_FILTER_ENABLED=true in your .env file');
        }

        if ($this->option('disable')) {
            $this->info('Note: To disable filtering, set EMOJI_FILTER_ENABLED=false in your .env file');
        }

        return 0;
    }

    private function showStatus(EmojiFilterService $filterService)
    {
        $this->info('🎯 Emoji Filter Status');
        $this->line('');

        $enabled = $filterService->isFilteringEnabled();
        $status = $enabled ? '<fg=green>ENABLED</fg=green>' : '<fg=red>DISABLED</fg=red>';
        
        $this->line("Status: {$status}");
        
        if ($enabled) {
            $blacklistCount = count(config('emoji-filter.blacklist', []));
            $this->line("Blacklisted emojis: <fg=yellow>{$blacklistCount}</fg=yellow>");
            
            $this->line('');
            $this->info('Active filters:');
            $this->line('  • Compound emojis: ' . (config('emoji-filter.filter_compound_emojis') ? '✅' : '❌'));
            $this->line('  • Skin tone variants: ' . (config('emoji-filter.filter_skin_tone_variants') ? '✅' : '❌'));
            $this->line('  • Duplicates: ' . (config('emoji-filter.filter_duplicates') ? '✅' : '❌'));
            $this->line('  • Debug logging: ' . (config('emoji-filter.log_filtering') ? '✅' : '❌'));
        } else {
            $this->warn('Filtering is disabled. All emojis will be shown including problematic ones.');
        }
    }

    private function showStats(EmojiFilterService $filterService)
    {
        $this->info('📊 Emoji Filter Statistics');
        $this->line('');

        $stats = $filterService->getFilterStats();
        
        $this->line("Total tested: <fg=cyan>{$stats['total_tested']}</fg=cyan>");
        $this->line("Actual problems: <fg=red>{$stats['actual_problems']}</fg=red>");
        $this->line("Problem rate: <fg=yellow>{$stats['problem_rate']}%</fg=yellow>");
        $this->line("Prediction accuracy: <fg=green>{$stats['prediction_accuracy']}%</fg=green>");
        $this->line("Current blacklist: <fg=magenta>{$stats['current_blacklist_count']}</fg=magenta>");
        $this->line("Last updated: <fg=blue>{$stats['last_updated']}</fg=blue>");

        $this->line('');
        if ($stats['filtering_enabled']) {
            $this->info('✅ Filtering is currently active');
        } else {
            $this->warn('⚠️  Filtering is currently disabled');
        }
    }

    private function showConfig(EmojiFilterService $filterService)
    {
        $this->info('⚙️  Emoji Filter Configuration');
        $this->line('');

        $config = $filterService->getConfigSummary();

        $this->line("Enabled: " . ($config['enabled'] ? '<fg=green>Yes</fg=green>' : '<fg=red>No</fg=red>'));
        $this->line("Blacklist count: <fg=yellow>{$config['blacklist_count']}</fg=yellow>");
        
        $this->line('');
        $this->info('Version rules:');
        foreach ($config['version_rules'] as $version => $rule) {
            $color = $rule === 'block' ? 'red' : 'yellow';
            $this->line("  Unicode {$version}: <fg={$color}>{$rule}</fg={$color}>");
        }

        $this->line('');
        $this->info('Factor rules:');
        foreach ($config['factor_rules'] as $factor => $level) {
            $color = $level === 'high_risk' ? 'red' : 'yellow';
            $this->line("  {$factor}: <fg={$color}>{$level}</fg={$color}>");
        }

        $this->line('');
        $this->info('Filter settings:');
        $this->line("  Compound emojis: " . ($config['filter_compound_emojis'] ? '<fg=green>Yes</fg=green>' : '<fg=red>No</fg=red>'));
        $this->line("  Skin tone variants: " . ($config['filter_skin_tone_variants'] ? '<fg=green>Yes</fg=green>' : '<fg=red>No</fg=red>'));
        $this->line("  Duplicates: " . ($config['filter_duplicates'] ? '<fg=green>Yes</fg=green>' : '<fg=red>No</fg=red>'));
        $this->line("  Debug logging: " . ($config['log_filtering'] ? '<fg=green>Yes</fg=green>' : '<fg=red>No</fg=red>'));

        $this->line('');
        $this->comment('💡 Tip: Modify config/emoji-filter.php or use .env variables to change settings');
        $this->comment('💡 Environment variables: EMOJI_FILTER_ENABLED, EMOJI_FILTER_COMPOUND, etc.');
    }
}