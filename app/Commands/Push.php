<?php

namespace App\Commands;

use App\Services\Config\ConfigService;
use App\Services\Config\Objects\Config;
use App\Services\Pusher\PusherService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Push extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'push {packageName?} {--P|push}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ConfigService $configService, PusherService $pusherService)
    {
        $pushToRepository = $this->option('push');
        $config = $configService->fetchConfig();
        $packageName = $this->argument('packageName');
        foreach ($config->packages as $package) {
            if ($packageName) {
                if ($packageName==$package->name) {
                    $pusherService->push($package, $pushToRepository);
                }
                continue;
            }
            $pusherService->push($package, $pushToRepository);
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
