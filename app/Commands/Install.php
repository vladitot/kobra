<?php

namespace App\Commands;

use App\Services\Config\ConfigService;
use App\Services\Installer\InstallerService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Install extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install {packageName?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install external packages into your project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ConfigService $configService, InstallerService $service)
    {
        $config = $configService->fetchConfig();
        $packageName = $this->argument('packageName');
        foreach ($config->packages as $package) {
            if ($packageName) {
                if ($packageName==$package->name) {
                    $service->install($package);
                }
                continue;
            }
            $service->install($package);
        }

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
