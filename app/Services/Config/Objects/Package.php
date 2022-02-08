<?php

namespace App\Services\Config\Objects;

use Illuminate\Support\Collection;

class Package
{
    public string $name;
    public string $type;
    public string $url;
    public string $reference;
    /** @var Collection|Path[] */
    public Collection $paths;

    public function __construct()
    {
        $this->paths = new Collection();
    }

    public function addPath(Path $path): void
    {
        $this->paths->push($path);
    }
}
