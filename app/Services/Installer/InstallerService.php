<?php

namespace App\Services\Installer;

use App\FileSystem\FileListsHelper;
use App\FileSystem\FileSystemHelper;
use App\Services\Config\Objects\Package;
use App\Services\Config\Objects\Path;
use App\Services\Git\GitService;

class InstallerService
{
    const GIT_PACKAGE_TYPE = 'git';
    private GitService $gitService;
    private FileSystemHelper $fileSystemHelper;
    private FileListsHelper $fileListHelper;


    public function __construct(GitService $gitService, FileSystemHelper $fileSystemHelper, FileListsHelper $fileListHelper)
    {
        $this->gitService = $gitService;
        $this->fileSystemHelper = $fileSystemHelper;
        $this->fileListHelper = $fileListHelper;
    }

    public function install(Package $package)
    {
        if ($package->type === self::GIT_PACKAGE_TYPE) {
            $this->runGit($package);
        }
    }

    private function runGit(Package $package)
    {
        $this->gitService->clone($package->url, $package->name);
        $this->gitService->checkout($package->reference, $package->name);
        $originFiles = $this->fileListHelper->getOriginFilesArray($package);
        $originToDestinationFileList = $this->fileListHelper->convertOriginFilesToCombinedPathList($package, $originFiles);
        $this->fileSystemHelper->copyFilesFromKeyToValue($originToDestinationFileList);
    }


}
