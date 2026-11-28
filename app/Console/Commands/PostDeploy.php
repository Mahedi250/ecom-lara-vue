<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PostDeploy extends Command
{
    /**
     * Usage after every deployment:
     *   php artisan app:post-deploy
     */
    protected $signature = 'app:post-deploy
                            {--optimize : Re-cache config/routes/views after clearing}';

    protected $description = 'Run all post-deployment steps: migrate, seed permissions, clear caches, restart queues';

    public function handle(): int
    {
        $this->info('Running post-deployment tasks...');

        // Clear caches FIRST so the config (e.g. config/admin-menu.php) is
        // fresh — a stale cached config would hide new menu permissions.
        $this->call('optimize:clear');

        $this->call('migrate', ['--force' => true]);
        $this->call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        $this->call('queue:restart');

        if ($this->option('optimize')) {
            $this->call('optimize');
        }

        $this->reloadPhpFpm();

        $this->newLine();
        $this->info('✔ Post-deployment done.');

        return self::SUCCESS;
    }

    /**
     * Best-effort PHP-FPM reload for OPcache staleness. The VPS runs aaPanel
     * (no Docker): aaPanel manages PHP-FPM via /etc/init.d/php-fpm-{version}
     * and sites live under /www/wwwroot. Only works when run as root —
     * otherwise the user is told to reload via the aaPanel UI.
     */
    private function reloadPhpFpm(): void
    {
        $scripts = is_dir('/etc/init.d')
            ? glob('/etc/init.d/php-fpm-*') ?: []
            : [];

        if ($scripts === []) {
            $this->warn('PHP-FPM auto-reload skipped (no aaPanel init script found).');
            $this->line('If OPcache has validate_timestamps=0, reload PHP in the aaPanel UI');
            $this->line('(App Store → PHP → Reload) or: /etc/init.d/php-fpm-82 reload');

            return;
        }

        foreach ($scripts as $script) {
            if (! is_executable($script)) {
                continue;
            }

            exec("{$script} reload 2>/dev/null", $output, $exitCode);

            if ($exitCode === 0) {
                $this->info(basename($script).' reloaded (OPcache cleared).');

                return;
            }
        }

        $this->warn('Could not reload PHP-FPM automatically (need root?).');
        $this->line('Reload PHP in the aaPanel UI: App Store → PHP → Reload.');
    }
}
