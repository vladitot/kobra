<?php

namespace App\Services\Git;

use App\FileSystem\FileSystemHelper;
use Symfony\Component\Process\Process;

class GitService
{

    private FileSystemHelper $helper;
    private \CzProject\GitPhp\Git $gitManager;

    public function __construct(FileSystemHelper $helper, \CzProject\GitPhp\Git $gitManager)
    {
        $this->helper = $helper;
        $this->gitManager = $gitManager;
    }

    public function clone(string $gitUrl, string $packageName) {
        shell_exec('mkdir -p '.$this->helper->getDirectoryNameByPackageName($packageName));

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

    /**
     * @throws \CzProject\GitPhp\GitException
     */
    public function checkout(string $where, string $packageName) {
        $repo = $this->gitManager->open($this->helper->getDirectoryNameByPackageName($packageName));
        $repo->execute('reset', '--hard', 'HEAD');
        $repo->execute('clean', '-fn');
        $repo->fetch();

        if (preg_match('/^v\d+\.\d+\.\d+/', $where)) {
            $repo->checkout($where);
            return;
        }

        $branches = $repo->getRemoteBranches();
        $originWhere = 'origin/'.$where;
        if (in_array($originWhere, $branches)) {
            $repo->checkout($where);
            $repo->execute('reset', '--hard', $originWhere);
        } else {
            $repo->checkout('master');
            $repo->createBranch($where, true);
        }
    }

    /**
     * @throws \CzProject\GitPhp\GitException
     */
    public function pushPackageToRepository(string $packageName, string $where)
    {
        $repo = $this->gitManager->open($this->helper->getDirectoryNameByPackageName($packageName));
        $repo->addAllChanges();
        $repo->commit("automated kobra push");
        $repo->push('origin', [$where]);
    }

}
