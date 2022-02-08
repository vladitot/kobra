<?php

namespace App\FileSystem;

class FileSystemHelper
{

    public function getDirContents($dir, &$results = array())
    {
        $files = scandir($dir);

        foreach ($files as $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[$path] = $path;
            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $results[$path] = $path;
            }
        }

        return $results;
    }

    public function copyFilesFromKeyToValue(array $files)
    {
        foreach ($files as $origin => $destination) {
            $this->copyFirstToSecond($origin, $destination);
        }
    }

    public function copyFirstToSecond($path1, $path2)
    {
        $path = pathinfo($path2);
        if (!file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0777, true);
        }
        if (!copy($path1, $path2)) {
            echo "copy failed \n";
        }
    }

    public function getOriginFilesFromPackage(string $packageName)
    {
        $files = $this->getDirContents(
            base_path(
                $this->getDirectoryNameByPackageName($packageName)
            )
        );
        return $files;
    }

    public function getDirectoryNameByPackageName(string $packageName): string {
        return 'kobra'.DIRECTORY_SEPARATOR.$packageName;
    }

}
