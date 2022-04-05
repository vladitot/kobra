<?php

namespace App\Commands;

use App\Services\Config\ConfigService;
use App\Services\Remover\RemoverService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Remove extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'remove {packageName?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove package files from your project directory';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ConfigService $configService, RemoverService $remover)
    {
        $config = $configService->fetchConfig();
        $packageName = $this->argument('packageName');
        foreach ($config->packages as $package) {
            if ($packageName) {
                if ($packageName==$package->name) {
                    $remover->remove($package);
                }
                continue;
            }
            $remover->remove($package);
        }
        shell_exec('chmod -R 777 infra');
        return 0;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
