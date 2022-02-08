<?php

namespace App\Services\Config\Objects;

use Illuminate\Support\Collection;

class Path
{
    public string $origin;
    public string $destination;
    public array $excludePaths;
}
