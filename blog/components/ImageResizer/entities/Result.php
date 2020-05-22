<?php


namespace blog\components\ImageResizer\entities;

/**
 * Class Result
 * @package blog\components\ImageResizer\entities
 */
class Result
{
    public $width;
    public $height;
    public $fileSize;
    public $fileName;
    public $resultPath;

    /**
     * Result constructor.
     * @param $width
     * @param $height
     * @param $resultPath
     */
    public function __construct(string $resultPath, int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->fileSize = new FileSize(filesize($resultPath));
        $this->fileName = pathinfo($resultPath, PATHINFO_BASENAME);
        $this->resultPath = $resultPath;
    }
}