<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileHelper
{
    private $tmpDir;
    //private $excelType;

    public function __construct($tmpDir)
    {
        $this->tmpDir = $tmpDir;
        //$this->excelType = $excelType;
    }

    public function getTmpDir()
    {
        return $this->tmpDir;
    }

    /*
     * Generates a file name with name $zipFileName, which is placed in the application tmpDir, the content of the zip file
     * is the same of the existent in the folder tmpDir/$dirName
     */
    public function generateZipFileFromDir($dirName, $zipFileName)
    {
        $dirPath = $this->tmpDir.'/'.$dirName;
        $zipFilePath = $this->tmpDir.$zipFileName;

        $zip = new \ZipArchive();
        if (true !== $zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            return false;
        }

        $this->zipDir(
                // base dir, note we use a trailing separator from now on
                rtrim($dirPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR,
                // subdir, empty on initial call
                null,
                // archive ref
                $zip
        );
        $zip->close();

        $response = new BinaryFileResponse($zipFilePath);
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $zipFileName);
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    private function zipDir($dir, $subdir, $zip)
    {
        // using real path
        $files = scandir($dir.$subdir);

        foreach ($files as $file) {
            if (in_array($file, [
                    '.',
                    '..',
            ])) {
                continue;
            }

            // check dir using real path
            if (is_dir($dir.$subdir.$file)) {
                // create folder using relative path
                $zip->addEmptyDir($subdir.$file);

                $this->zipDir($dir, // remember base dir
$subdir.$file.DIRECTORY_SEPARATOR, // relative path, don't forget separator
$zip) // archive
;
            }

            // file
            else {
                // get real path, set relative path
                $zip->addFile($dir.$subdir.$file, $subdir.$file);
            }
        }
    }

    public function saveExcelFileToTempDir($targetFileName, $objPHPExcel, $tempDirName)
    {
        if (!file_exists($this->tmpDir.$tempDirName)) {
            mkdir($this->tmpDir.$tempDirName);
        }
        $targetFile = $this->tmpDir.$tempDirName.'/'.$targetFileName;
        //$objWriter = IOFactory::createWriter($objPHPExcel, $this->excelType);
       // $objWriter->setIncludeCharts(true);
       // $objWriter->save($targetFile);
    }

    /*
     * Returns a random directory name that DOESN'T exists in the directory passed by argument
     * @throws \Exception
     */
    public function getTempDirRandomName($dirPath = null)
    {
        if (!$dirPath) {
            $dirPath = $this->tmpDir;
        }

        $found = false;
        $randomDirName = 0;

        while (!$found) {
            $randomDirName = random_int(0, 9999999999);
            if (!file_exists($dirPath.$randomDirName)) {
                mkdir($dirPath.$randomDirName);
                $found = true;
            }
        }

        return $randomDirName;
    }

    public function createDir($dirPath)
    {
        if ('' == $dirPath) {
            throw new \Exception('createDir: Cant create a directory with an empty name');
        }
        $dirPath = $this->tmpDir.$dirPath;
        if (!file_exists($dirPath)) {
            mkdir($dirPath);
        }
    }

    public function copyFile($sourceFileFullPath, $randomDir, $destFileName)
    {
        if ('' == $destFileName) {
            throw new \Exception('copyFile: Cant create a file with an empty name');
        }
        $targetFileFullPath = $this->tmpDir.$randomDir.'/'.$destFileName;
        copy($sourceFileFullPath, $targetFileFullPath);
    }

    public function serveFileAsStream($filePath, $fileName)
    {
        $response = new Response();
        $response->setContent(file_get_contents($filePath));
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', 'filename='.$fileName);

        return $response;
    }
}
