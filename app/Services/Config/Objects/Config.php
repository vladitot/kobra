<?php

namespace App\Services\Config\Objects;

use Illuminate\Support\Collection;

class Config
{
    /** @var Collection|CustomCommand[] */
    public Collection $customCommand;

    /** @var Collection|Package[]  */
    public Collection $packages;
}
