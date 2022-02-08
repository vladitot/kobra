<?php

namespace App\Services\Config\Objects;

use Illuminate\Support\Collection;

class Path
{
    public string $origin;
    public string $destination;
    public bool $appendFileNamesWithPackageName;
    /** @var Collection|string[]  */
    public Collection $excludePaths;
}
