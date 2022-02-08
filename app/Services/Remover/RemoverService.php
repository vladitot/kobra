<?php

namespace App\Services\Remover;

use App\FileSystem\FileListsHelper;
use App\FileSystem\FileSystemHelper;
use App\Services\Config\Objects\Package;
use App\Services\Installer\InstallerService;

class RemoverService
{

    private FileSystemHelper $fileSystemHelper;
    private FileListsHelper $fileListHelper;

    public function __construct(FileSystemHelper $fileSystemHelper, FileListsHelper $fileListHelper)
    {
        $this->fileSystemHelper = $fileSystemHelper;
        $this->fileListHelper = $fileListHelper;
    }

    public function remove(Package $package)
    {
        if ($package->type === InstallerService::GIT_PACKAGE_TYPE) {
            $this->runGit($package);
        }
    }

    private function runGit(Package $package)
    {
        $originFiles = $this->fileListHelper->getOriginFilesArray($package);
        $originToDestinationFileList = $this->fileListHelper->convertOriginFilesToCombinedPathList($package, $originFiles);
        $this->fileSystemHelper->removeFilesFromValue(
            $originToDestinationFileList
        );
    }
}
