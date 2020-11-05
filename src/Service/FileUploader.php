<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $slugger;

    private $uploadRoot;

    public function __construct(SluggerInterface $slugger, string $projectDir)
    {
        $this->slugger = $slugger;
        $this->uploadRoot = $projectDir;

    }

    /**
     * @param UploadedFile $file
     * @param string $targetDir
     * @param string|null $fileName
     * @return string
     */
    public function upload(UploadedFile $file, string $targetDir, string $fileName = null): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $extension = $file->guessExtension() ?? $file->getExtension();
        $fileName = $fileName === null ? $safeFilename . '-' . uniqid() . '.' . $extension : $fileName;

        $targetDir = $this->uploadRoot . $targetDir;

        if(!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        dump($targetDir, $fileName);

        $file->move($targetDir, $fileName);

        return $fileName;
    }
}