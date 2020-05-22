<?php


namespace blog\components\ImageResizer;


use blog\components\ImageResizer\entities\Path;
use blog\components\ImageResizer\exceptions\ImageResizerException;
use blog\components\ImageResizer\interfaces\ImageResizerDriverInterface;

/**
 * Class ImageResizer
 * @package blog\components\ImageResizer
 */
final class ImageResizer
{
    private $driver;

    /**
     * Resizer constructor.
     * @param $driver
     */
    public function __construct(ImageResizerDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return ImageResizerDriverInterface
     */
    public function driver(): ImageResizerDriverInterface
    {
        return $this->driver;
    }

    /**
     * @param Path $path
     * @return bool
     */
    public function isImage(Path $path): bool
    {
        try {
            $this->driver->create($path);
        } catch (ImageResizerException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param Path $path
     * @return ImageResizerDriverInterface
     * @throws ImageResizerException
     */
    public function create(Path $path): ImageResizerDriverInterface
    {
        try {
            return $this->driver->create($path);
        } catch (ImageResizerException $exception) {
            throw $exception;
        }
    }
}