<?php


namespace api\services;


use api\models\PostImageForm;
use blog\components\ImageResizer\interfaces\ImageResizerDriverInterface;
use blog\components\PathReplacer\PathReplacer;
use blog\components\PathReplacer\PathReplacerExceptions;

/**
 * Class PostImageService
 * @package api\services
 */
class PostImageService
{
    private $pathReplacer;

    /**
     * PostImageManager constructor.
     * @param PathReplacer $pathReplacer
     */
    public function __construct(PathReplacer $pathReplacer)
    {
        $this->pathReplacer = $pathReplacer;
    }

    /**
     * @param PostImageForm $form
     * @param ImageResizerDriverInterface $driver
     * @return PathReplacer
     * @throws PathReplacerExceptions
     */
    public function frontFilePath(PostImageForm $form, ImageResizerDriverInterface $driver): PathReplacer
    {
        return clone $this->pathReplacer->setVars([
            'type' => $form->type,
            'postId' => $form->postId,
            'fileName' => $form->fileName,
            'width' => $driver->getCurrentWidth(),
            'height' => $driver->getCurrentHeight(),
            'ext' => $driver->getTargetExtension()
        ])->replace('front:{postImage}/{fileName}_{width}x{height}.{ext}');
    }

    /**
     * @param PostImageForm $form
     * @return string
     * @throws PathReplacerExceptions
     */
    public function frontDirPath(PostImageForm $form): string
    {
        return $this->pathReplacer
            ->setVars(['postId' => $form->postId, 'type' => $form->type])
            ->replace('front:{postImage}')->getPath();
    }

    /**
     * @param string $path
     * @return string
     * @throws PathReplacerExceptions
     */
    public function frontDeletePath(string $path): string
    {
        return $this->pathReplacer->replace("front:{public}/{$path}")->getPath();
    }

    /**
     * @param string $path
     * @return string
     * @throws PathReplacerExceptions
     */
    public function pathToUrl(string $path): string
    {
        return $this->pathReplacer->replace($path)->getUrl();
    }
}