<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Upload a file at a targeted directory
 */
class FileUploader
{
    /**
     * The targeted directory path
     *
     * @var String
     */
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Upload the file
     *
     * @param UploadedFile $file
     * @return String the file name
     */
    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // create a unique name
        $fileName = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    /**
     * Delete a file
     *
     * @param String $filename
     * @return Boolean true if success, false if not
     */
    public function deleteFile($filename)
    {
        
        try {
            if (file_exists ($this->targetDirectory.'/'. $filename) && $filename != null) {
                unlink($this->targetDirectory.'/'. $filename);
            }

            return true;
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            return false;
        }

       
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}