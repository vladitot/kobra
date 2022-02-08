<?php

namespace App\Services\Config\Objects;

use Illuminate\Support\Collection;

class Package
{
    public string $name;
    public string $type;
    public string $url;
    public string $referneceType;
    public string $referenceName;
    /** @var Collection|Path[]  */
    public Collection $paths;
}
