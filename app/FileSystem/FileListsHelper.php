<?php

namespace App\FileSystem;

use App\Services\Config\Objects\Package;

class FileListsHelper
{

    private FileSystemHelper $helper;

    public function __construct(FileSystemHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * сконвертируем список файлов в список origin=>destination
     */
    public function convertOriginFilesToCombinedPathList(Package $package, array $files): array
    {
        foreach ($package->paths as $path) {
            foreach ($files as $origin=>&$destination) {
                if (
                    $path->origin==$origin
                    ||
                    preg_match('/^'.preg_quote($path->origin, '/').'\//', $origin)

                ) {
                    $destination = str_replace($path->origin, $path->destination, $destination);
                };
            }
        }
        return $files;
    }

    /**
     * сконвертируем список файлов в список origin=>destination
     * но для обратной процедуры (че там push или load)
     */
    private function convertDestinationFilesToCombinedPathList(Package $package, array $files): array
    {
        foreach ($package->paths as $path) {
            foreach ($files as &$destination) {
                $destination = str_replace($path->origin, $path->destination, $destination);
            }
        }
        return $files;
    }

    public function getOriginFilesArray(Package $package): array
    {
        //lets try make origin=>destination
        //не забыть, что origin указывается относительно директории с файлами пакета
        //а destination относительно корня проекта (нашего)
        $files = $this->helper->getOriginFilesFromPackage($package->name);
        $resultFiles = [];
        foreach ($package->paths as $path) {
            foreach ($files as $origin => $destination) {
                if (preg_match('/^' . preg_quote($path->origin, '/') . '/', $origin)) {
                    $resultFiles[$origin] = $destination;
                }
            }

            foreach ($path->excludePaths as $excludePath) {
                foreach ($resultFiles as $origin => $destination) {
                    if (preg_match('/^' . preg_quote($excludePath, '/') . '/', $origin)) {
                        unset($resultFiles[$origin]);
                    }
                }
            }
        }
        return $resultFiles;

    }

    public function getDestinationFilesArray(Package $package): array
    {
        // здесь возвращаем destination=>destination
        $files = $this->helper->getDestinationFilesByPackagePaths($package);
        return $files;
    }

    public function convertDestinationFilesToCombined(array $files, Package $package): array {
        $resultFiles = [];

        foreach ($package->paths as $path) {
            foreach ($files as $destination) {
                if (preg_match('/^'.preg_quote($path->destination).'/', $destination)) {
                    $resultFiles[str_replace($path->destination, $path->origin, $destination)] = $destination;
                }
            }

            foreach ($path->excludePaths as $excludePath) {
                foreach ($resultFiles as $origin => $destination) {
                    if (preg_match('/^' . preg_quote($excludePath, '/') . '/', $origin)) {
                        unset($resultFiles[$origin]);
                    }
                }
            }

        }
        return $resultFiles;
    }
}
