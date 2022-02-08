<?php

namespace App\FileSystem;

class FileSystemHelper
{

    public function getDirContents($dir, string $packageName, &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $storePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
                $storePath = str_replace($this->getDirectoryNameByPackageName($packageName) . DIRECTORY_SEPARATOR, '', $storePath);
                $results[$storePath] = $storePath;
            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $packageName, $results);
                $storePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
                $storePath = str_replace($this->getDirectoryNameByPackageName($packageName) . DIRECTORY_SEPARATOR, '', $storePath);
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
            $this->copyFirstToSecond($origin, $destination, $originPrefix);
        }
    }

    public function copyFirstToSecond(string $path1, string $path2, string $originPrefix = '')
    {
        $path = pathinfo($path2);
        if (!file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0777, true);
        }
        $firstPath = rtrim($originPrefix, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path1;
        if (!is_dir($firstPath)) {
            if (!copy($firstPath, $path2)) {
                echo "copy failed \n";
            }
        }
    }

    public function getOriginFilesFromPackage(string $packageName)
    {
        $files = $this->getDirContents(
            base_path(
                $this->getDirectoryNameByPackageName($packageName)
            ), $packageName
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
