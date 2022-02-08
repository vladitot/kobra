<?php

namespace App\Services\Config;

use App\Services\Config\Objects\Config;
use App\Services\Config\Objects\CustomCommand;
use App\Services\Config\Objects\Package;
use App\Services\Config\Objects\Path;
use Illuminate\Support\Collection;

class ConfigService
{
    public function fetchConfig(): Config
    {
        $json = json_decode(file_get_contents(base_path() . DIRECTORY_SEPARATOR . 'kobra.json'), true);

        $config = new Config();
        foreach ($json['customCommands'] as $command) {
            $config->addCommand($this->createCustomCommand($command));
        }

        foreach ($json['packages'] as $package) {
            $config->addPackage($this->createPackage($package));
        }
        return $config;
    }

    private function createCustomCommand(array $data): CustomCommand
    {
        $command = new CustomCommand();
        $command->command = $data['command'];
        $command->description = $data['description'];
        $command->alias = $data['alias'];
        return $command;
    }

    private function createPackage(array $packageArray): Package
    {
        $package = new Package();
        $package->name = $packageArray['name'];
        $package->reference = $packageArray['reference'];
        $package->type = $packageArray['type'];
        $package->url = $packageArray['url'];

        foreach ($packageArray['paths'] as $pathArray) {
            $path = new Path();
            $path->appendFileNamesWithPackageName = $pathArray['appendFileNamesWithPackageName'];
            $path->destination = $pathArray['destination'];
            $path->excludePaths = $pathArray['excludePaths'] ?? [];
            $path->origin = $pathArray['origin'];
            $package->addPath($path);
        }
        return $package;
    }
}


