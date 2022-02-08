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
                    if ($path->appendFileNamesWithPackageName) {
                        $destination = $this->appendFileWithPackageName($destination, $package->name);
                    }
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

    public function appendFileWithPackageName(string $destination, string $packageName)
    {
        $pathInfo = pathinfo($destination);
        $destination = str_replace($pathInfo['basename'], '', $destination);
        if (isset($pathInfo['extension'])) {
            return rtrim($destination, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '-' . $packageName . '.'.$pathInfo['extension'];
        } else {
            return rtrim($destination, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '-' . $packageName;
        }

    }
}
