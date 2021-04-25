<?php

namespace App\Service;

use App\Entity\MediaContent;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use ReflectionException;
use Symfony\Component\HttpFoundation\File\File;

class FileHelper
{

    const THUMBNAIL_MAX_WIDTH = 200;
    const THUMBNAIL_MAX_HEIGHT = 250;

    /**
     * @param MediaContent $file
     * @return bool
     * @throws ReflectionException
     */
    public function isImage(MediaContent $file): bool
    {
        return preg_match('/^image\\//', $file->getFileType());
    }

    /**
     * @param MediaContent $file
     * @return File
     */
    public function getThumbnail(MediaContent $file): ?File
    {
        $location = $this->thumbnailLocation($file);

        if (!file_exists($location)) {
            $this->generateThumbnail($file);
        }

        if (!file_exists($location)) {
            return null;
        }

        return new File($location);
    }

    private function thumbnailLocation(MediaContent $file): string
    {
        $locationStr = $file->getLocation();
        $index = strrpos($locationStr, ".");
        return ($index === false) ? "" :
            substr($locationStr, 0, $index) .
            '.thumb.' .
            substr($locationStr, $index + 1);
    }

    public function generateThumbnail(MediaContent $content)
    {
        $imagine = new Imagine();

        $image = $imagine->open($content->getLocation());

        list($iwidth, $iheight) = getimagesize($content->getLocation());
        $ratio = $iwidth / $iheight;
        $width = self::THUMBNAIL_MAX_WIDTH;
        $height = self::THUMBNAIL_MAX_HEIGHT;

        if (self::THUMBNAIL_MAX_WIDTH / self::THUMBNAIL_MAX_HEIGHT > $ratio) {
            $width = self::THUMBNAIL_MAX_HEIGHT * $ratio;
        } else {
            $height = self::THUMBNAIL_MAX_WIDTH / $ratio;
        }

        $image->thumbnail(new Box($width, $height))
            ->save($this->thumbnailLocation($content));
    }
}
