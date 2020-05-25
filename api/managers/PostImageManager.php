<?php


namespace api\managers;


use api\models\PostImageForm;
use api\services\PostImageService;
use blog\components\ImageResizer\entities\Path;
use blog\components\ImageResizer\entities\Size;
use blog\components\ImageResizer\exceptions\ImageResizerException;
use blog\components\ImageResizer\ImageResizer;
use blog\components\PathReplacer\PathReplacer;
use blog\components\PathReplacer\PathReplacerExceptions;

/**
 * Class PostImageManager
 * @package api\managers
 */
class PostImageManager
{
    private $service;
    private $resizer;

    /**
     * PostImageManager constructor.
     * @param ImageResizer $resizer
     * @param PostImageService $service
     */
    public function __construct(ImageResizer $resizer, PostImageService $service)
    {
        $this->resizer = $resizer;
        $this->service = $service;
    }

    /**
     * @param PostImageForm $form
     * @return PathReplacer
     * @throws ImageResizerException
     * @throws PathReplacerExceptions
     */
    public function marginalResize(PostImageForm $form): PathReplacer
    {
        $imagick = $this->resizer
            ->create(new Path($form->image->tempName))
            ->marginalResize(new Size($form->with, $form->height))
            ->compress($form->compress)
            ->modulate()
            ->strip();

        $form->type = PostImageForm::TYPE_RESIZED;
        $replacer = $this->service->frontFilePath($form, $imagick);
        $imagick->save(new Path($replacer->existIncrement()));

        return $replacer;
    }

    /**
     * @param PostImageForm $form
     * @return PathReplacer
     * @throws ImageResizerException
     * @throws PathReplacerExceptions
     */
    public function original(PostImageForm $form): PathReplacer
    {
        $imagick = $this->resizer
            ->create(new Path($form->image->tempName))
            ->strip();

        $form->type = PostImageForm::TYPE_ORIGINAL;
        $replacer = $this->service->frontFilePath($form, $imagick);
        $imagick->save(new Path($replacer->existIncrement()));

        return $replacer;
    }

    /**
     * @param PostImageForm $form
     * @param string $type
     * @return array
     * @throws PathReplacerExceptions
     */
    public function filesList(PostImageForm $form, string $type): array
    {
        $form->type = $type;

        return array_map(function ($file) {
            return $this->service->pathToUrl($file);
        }, glob($this->service->frontDirPath($form) . '/*.[jpg][png][gif]'));
    }

    /**
     * @param PostImageForm $form
     * @return bool
     * @throws PathReplacerExceptions
     */
    public function delete(PostImageForm $form): bool
    {
        if (file_exists($path = $this->service->frontDeletePath($form->path))) {
            return unlink($path);
        }

        return false;
    }
}