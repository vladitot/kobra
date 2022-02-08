<?php

namespace App\Services\Config\Objects;

use Illuminate\Support\Collection;

class Config
{

    /** @var Collection|CustomCommand[] */
    public Collection $customCommands;

    /** @var Collection|Package[] */
    public Collection $packages;

    public function __construct()
    {
        $this->customCommands = new Collection();
        $this->packages = new Collection();
    }

    public function addCommand(CustomCommand $command) {
        $this->customCommands->push($command);
    }

    public function addPackage(Package $package)
    {
        $this->packages->push($package);
    }
}

