<?php

namespace App\Services\Git;

use App\FileSystem\FileSystemHelper;
use Symfony\Component\Process\Process;

class GitService
{

    private FileSystemHelper $helper;

    public function __construct(FileSystemHelper $helper)
    {
        $this->helper = $helper;
    }

    public function clone(string $gitUrl, string $packageName) {
        $cmd = 'git clone '.$gitUrl.' '.$this->helper->getDirectoryNameByPackageName($packageName);
        $process = new Process(explode(' ', $cmd));
        $process->run();
    }

    public function checkout(string $where, string $packageName) {
        $cmd = 'cd '
            .$this->helper->getDirectoryNameByPackageName($packageName)
            .' && git checkout '.$where;
        $process = new Process(explode(' ', $cmd));
        $process->run();
        echo $process->getOutput();
        echo $process->getErrorOutput();
    }

}
