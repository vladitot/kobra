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
        $process->wait();
        echo $process->getOutput();
        echo $process->getErrorOutput();
        if ($process->getExitCode()!=0) {
            echo "Trying to make git pull...\n";
            $cmd = 'cd '
                .$this->helper->getDirectoryNameByPackageName($packageName)
                .' && git fetch --all && git pull';
            $result = shell_exec($cmd);
            echo $result;
        }
    }

    public function checkout(string $where, string $packageName) {
        $cmd = 'cd '
            .$this->helper->getDirectoryNameByPackageName($packageName)
            .' && git fetch --all && git checkout '.$where. ' && git pull';
        $result = shell_exec($cmd);
        echo $result;
    }

    public function pushPackageToRepository(string $packageName)
    {
        $cmd = 'cd '
            .$this->helper->getDirectoryNameByPackageName($packageName)
            .' && git add . && git commit -m "automated kobra push" && git push';
        $result = shell_exec($cmd);
        echo $result;
    }

}
