<?php

namespace App\Services\Pusher;

use App\FileSystem\FileListsHelper;
use App\FileSystem\FileSystemHelper;
use App\Services\Git\GitService;

class PusherService
{

    private GitService $gitService;
    private FileSystemHelper $fileSystemHelper;
    private FileListsHelper $fileListHelper;

    public function __construct(GitService $gitService, FileSystemHelper $fileSystemHelper, FileListsHelper $fileListHelper)
    {
        $this->gitService = $gitService;
        $this->fileSystemHelper = $fileSystemHelper;
        $this->fileListHelper = $fileListHelper;
    }

    public function push($package)
    {
        $this->gitService->clone($package->url, $package->name);
        $this->gitService->checkout($package->reference, $package->name);
        $allDestinationFiles = $this->fileListHelper->getDestinationFilesArray($package);

        $destinationFilesKeyedByOrigin = $this->fileListHelper
            ->convertDestinationFilesToCombined($allDestinationFiles, $package);

        $this->fileSystemHelper->copyFilesFromValueToKey(
            $destinationFilesKeyedByOrigin,
            $this->fileSystemHelper->getDirectoryNameByPackageName($package->name)
        );
    }
}
