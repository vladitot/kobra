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
//        $configArray = json_decode(file_get_contents('kobra.json'), true);
        $configArray = yaml_parse_file('kobra.yml');

        $config = new Config();
        if (isset($configArray['customCommands'])) {
            foreach ($configArray['customCommands'] as $command) {
                $config->addCommand($this->createCustomCommand($command));
            }
        }

        foreach ($configArray['packages'] as $package) {
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
        $package->installReference = $packageArray['install-reference'];
        $package->pushReference = $packageArray['push-reference'];
        $package->type = $packageArray['type'];
        $package->url = $packageArray['url'];
        foreach ($packageArray['paths'] as $destinationPack) {
            foreach ($destinationPack as $destinationLeft=>$originRight) {
                $path = new Path();
                if ($destinationLeft == 'excludePaths') continue;
                $path->excludePaths = $destinationPack['excludePaths'] ?? [];
                $path->destination = $destinationLeft;
                $path->origin = $originRight;
                $package->addPath($path);
            }
        }
        return $package;
    }
}


