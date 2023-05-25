<?php
/**
 * @author hR.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

abstract class AbstractFileUploader
{
    private $targetDirectory;
    private $slugger;
    private $flashBag;

    /**
     * @param string            $targetDirectory
     * @param SluggerInterface  $slugger
     * @param FlashBagInterface $flashBag
     */
    public function __construct(string $targetDirectory, SluggerInterface $slugger, FlashBagInterface $flashBag)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->flashBag = $flashBag;
    }

    /**
     * @param UploadedFile $file
     * @param string|null  $oldFilename
     *
     * @return string
     */
    public function upload(UploadedFile $file, ?string $oldFilename): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            if ($oldFilename) {
                $oldFullPath = $this->getTargetDirectory().'/'.$oldFilename;
                if (is_file($oldFullPath)) {
                    unlink($oldFullPath); //remove old file
                }
            }

            $file->move($this->getTargetDirectory(), $fileName);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            if (in_array($fileExtension, ['docx', 'doc', 'xlsx', 'xls'])) {
                $baseFilePath = $this->getTargetDirectory().'/';
                $originalFilePath = $baseFilePath.'/'.$fileName;
                shell_exec("/usr/bin/libreoffice --headless --convert-to pdf:writer_pdf_Export $originalFilePath --outdir $baseFilePath");
            }
        } catch (FileException $e) {
            $this->flashBag->add('error', 'Erreur : Le fichier n\'a pas pu être uploadé. ' . $e->getMessage());
        }

        return $fileName;
    }

    /**
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}