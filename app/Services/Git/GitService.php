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
            $commands = [
                'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git reset --hard HEAD',
                'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git clean -fd',
                'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git fetch --all',
                'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git checkout master',
                'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git reset --hard origin/master',
            ];

            $this->runCommands($commands);
        }
    }

    public function runCommands(array $commands) {
        foreach ($commands as $command) {
            $result = shell_exec($command);
            echo $result;
        }
    }

    public function checkout(string $where, string $packageName) {
        $commands = [
                'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git reset --hard HEAD',
                'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git fetch --all'
            ];
            if (preg_match('/^v\d+\.\d+\.\d+/', $where)) {
                $commands[]='cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git checkout '.$where;;
            } else {
                $commands[]='cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git checkout -B '.$where.' origin/'.$where;
            }

        $this->runCommands($commands);
    }

    public function pushPackageToRepository(string $packageName, string $where)
    {
        $commands =[
            'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git add .',
            'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' && git commit -m "automated kobra push"',

            'cd ' .$this->helper->getDirectoryNameByPackageName($packageName).' &&  git push --set-upstream origin '.$where,
        ];
        $this->runCommands($commands);
    }

}
