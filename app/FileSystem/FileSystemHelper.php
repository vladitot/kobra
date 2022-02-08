<?php

namespace App\FileSystem;

use App\Services\Config\Objects\Package;

class FileSystemHelper
{

    public function getOriginDirContents(string $dir, string $packageName, &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $storePath = str_replace(rtrim(getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, '', $path);
                $storePath = str_replace($this->getDirectoryNameByPackageName($packageName) . DIRECTORY_SEPARATOR, '', $storePath);
                $results[$storePath] = $storePath;
            } else if ($value != "." && $value != "..") {
                $this->getOriginDirContents($path, $packageName, $results);
                $storePath = str_replace(rtrim(getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, '', $path);
                $storePath = str_replace($this->getDirectoryNameByPackageName($packageName) . DIRECTORY_SEPARATOR, '', $storePath);
                $results[$storePath] = $storePath;
            }
        }

        return $results;
    }

    public function getDestinationDirContents(string $dir, &$results = [])
    {
        if (!is_dir($dir)) {
            return [$dir=>$dir];
        }
        $files = scandir($dir);

        foreach ($files as $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $storePath = str_replace(rtrim(getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, '', $path);
                $results[$storePath] = $storePath;
            } else if ($value != "." && $value != "..") {
                $this->getDestinationDirContents($path, $results);
                $storePath = str_replace(rtrim(getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, '', $path);
                $results[$storePath] = $storePath;
            }
        }

        return $results;
    }

    public function removeFilesFromValue(array $files)
    {
        foreach ($files as $destination) {
            $this->removePathRecursively($destination);
        }
    }

    public function copyFilesFromKeyToValue(array $files, string $originPrefix = '')
    {
        foreach ($files as $origin => $destination) {
            $this->copyFirstToSecond(
                rtrim($originPrefix, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $origin,
                $destination
            );
        }
    }

    public function copyFilesFromValueToKey(array $files, string $originPrefix = '')
    {
        foreach ($files as $origin => $destination) {
            $originFileName = rtrim($originPrefix, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $origin;
            if (file_exists($originFileName)) {
                if (!is_dir($originFileName)) {
                    unlink($originFileName);
                }
            }
            $this->copyFirstToSecond(
                $destination,
                rtrim($originPrefix, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $origin
            );
        }
    }

    public function copyFirstToSecond(string $path1, string $path2)
    {
        $path = pathinfo($path2);
        if (!file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0777, true);
        }

        if (!is_dir($path1)) {
            if (!copy($path1, $path2)) {
                echo "copy failed \n";
            }
        }
    }

    public function getDestinationFilesByPackagePaths(Package $package)
    {
        $files = [];
        foreach ($package->paths as $path) {
            $files = array_merge($files, $this->getDestinationDirContents($path->destination));
        }

        return $files;
    }

    public function getOriginFilesFromPackage(string $packageName)
    {
        $files = $this->getOriginDirContents(

                $this->getDirectoryNameByPackageName($packageName),
                $packageName
        );
        return $files;
    }

    public function getDirectoryNameByPackageName(string $packageName): string
    {
        return 'infra-vendor' . DIRECTORY_SEPARATOR . $packageName;
    }


    private function removePathRecursively($path)
    {
        if (!is_dir($path)) {
            if (file_exists($path)) unlink($path);
        }
        $files = glob($path . '/*');
        foreach ($files as $file) {
            if (!is_dir($file)) {
                if (file_exists($path)) unlink($file);
            } else {
                $this->removePathRecursively($file);
            }
        }
        if (file_exists($path)) rmdir($path);

    }

}
