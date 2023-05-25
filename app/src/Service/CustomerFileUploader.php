<?php
/**
 * @author hR.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class CustomerFileUploader extends AbstractFileUploader
{
    /**
     * @param string            $targetDirectory
     * @param SluggerInterface  $slugger
     * @param FlashBagInterface $flashBag
     */
    public function __construct(string $targetDirectory, SluggerInterface $slugger, FlashBagInterface $flashBag)
    {
        parent::__construct($targetDirectory, $slugger, $flashBag);
    }
}