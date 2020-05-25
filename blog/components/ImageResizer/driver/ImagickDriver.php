<?php


namespace blog\components\ImageResizer\driver;


use blog\components\ImageResizer\entities\Path;
use blog\components\ImageResizer\entities\Result;
use blog\components\ImageResizer\entities\Size;
use blog\components\ImageResizer\exceptions\ImageResizerException;
use blog\components\ImageResizer\interfaces\ImageResizerDriverInterface;
use blog\components\ImageResizer\interfaces\ImageResizerSettingsInterface;
use blog\components\ImageResizer\settings\ImagickSettings;
use Imagick;
use ImagickException;

/**
 * Class ImagickDriver
 * @property Imagick $imagick
 * @property ImagickSettings $settings
 *
 * @package blog\components\ImageResizer\driver
 */
final class ImagickDriver implements ImageResizerDriverInterface
{
    private $settings;
    private $imagick;

    /**
     * ImagickDriver constructor.
     * @param ImageResizerSettingsInterface $settings
     */
    public function __construct(ImageResizerSettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param Path $path
     * @return ImageResizerDriverInterface
     * @throws ImageResizerException
     */
    public function create(Path $path): ImageResizerDriverInterface
    {
        try {
            $this->imagick = new Imagick($path->get());
        } catch (ImagickException $exception) {
            throw new ImageResizerException('Fail to create driver', 0, $exception);
        }

        return $this;
    }

    /**
     * @param Size $size
     * @return ImageResizerDriverInterface
     * @throws ImageResizerException
     */
    public function resize(Size $size): ImageResizerDriverInterface
    {
        try {
            $this->imagick->resizeImage(
                $size->getWith(),
                $size->getHeight(),
                $this->settings->getResize()->getFilter(),
                $this->settings->getResize()->getBlur(),
                $this->settings->getResize()->getBestfit()
            );
        } catch (ImagickException $e) {
            throw new ImageResizerException($e->getMessage(), $e->getCode(), $e);
        }


        return $this;
    }

    /**
     * @param Size $size
     * @return ImageResizerDriverInterface
     * @throws ImageResizerException
     */
    public function adaptiveResize(Size $size): ImageResizerDriverInterface
    {
        try {
            $this->imagick->adaptiveResizeImage(
                $size->getWith(),
                $size->getHeight(),
                $this->settings->getResize()->getBestfit()
            );
        } catch (ImagickException $e) {
            throw new ImageResizerException($e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * @param Size $size
     * @return ImageResizerDriverInterface
     * @throws ImageResizerException
     */
    public function crop(Size $size): ImageResizerDriverInterface
    {
        try {
            $this->imagick->cropImage($size->getWith(), $size->getHeight(), $size->getLeft(), $size->getTop());
        } catch (ImagickException $e) {
            throw new ImageResizerException($e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }

    /**
     * @param Size $size
     * @return ImageResizerDriverInterface
     * @throws ImageResizerException
     */
    public function marginalResize(Size $size): ImageResizerDriverInterface
    {
        $targetWidth = $size->getWith();
        $targetHeight = $size->getHeight();

        if ($this->isCurrentLandscape()) {
            $targetHeight = round($this->getCurrentHeight() * $size->getWith() / $this->getCurrentWidth());
        } elseif ($this->isCurrentPortrait()) {
            $targetWidth = round($this->getCurrentWidth() * $size->getHeight() / $this->getCurrentHeight());
        }

        $this->resize(new Size($targetWidth, $targetHeight));
        $this->crop(new Size($size->getWith(), $size->getHeight()));

        return $this;
    }

    /**
     * @return ImageResizerDriverInterface
     */
    public function postProcessing(): ImageResizerDriverInterface
    {
        $this->imagick->posterizeImage(136, false);
        $this->imagick->transformImageColorspace(Imagick::COLORSPACE_SRGB);
        $this->imagick->setInterlaceScheme(Imagick::INTERLACE_NO);

        return $this;
    }

    /**
     * @return ImageResizerDriverInterface
     */
    public function modulate(): ImageResizerDriverInterface
    {
        $this->imagick->modulateImage(
            $this->settings->getModulate()->getBrightness(),
            $this->settings->getModulate()->getSaturation(),
            $this->settings->getModulate()->getHue()
        );

        return $this;
    }

    /**
     * @return ImageResizerDriverInterface
     */
    public function sharp(): ImageResizerDriverInterface
    {
        $this->imagick->unsharpMaskImage(
            $this->settings->getSharp()->getRadius(),
            $this->settings->getSharp()->getSigma(),
            $this->settings->getSharp()->getAmount(),
            $this->settings->getSharp()->getThreshold()
        );

        return $this;
    }

    /**
     * @return ImageResizerDriverInterface
     */
    public function compress(): ImageResizerDriverInterface
    {
        if ($this->settings->getFormat()->getExtension() == 'png') {
            $this->pngCompress();
        } elseif ($this->settings->getFormat()->getExtension() == 'jpg') {
            $this->jpegCompress();
        }

        return $this;
    }

    /**
     * @return ImageResizerDriverInterface
     */
    public function strip(): ImageResizerDriverInterface
    {
        $profiles = $this->imagick->getImageProfiles('icc', true);
        $this->imagick->stripImage();

        if(!empty($profiles)) {
            $this->imagick->profileImage('icc', $profiles['icc']);
        }

        return $this;
    }

    /**
     * @param Path $path
     * @param bool $makePath
     * @return Result
     * @throws ImageResizerException
     */
    public function save(Path $path, bool $makePath = true): Result
    {
        $e = $this->settings->getFormat()->getExtension();

        if (!$this->imagick->writeImage("{$e}:{$path->create($makePath)->get()}")) {
            throw new ImageResizerException('Fail to save');
        }

        $result = new Result($path, $this->imagick->getImageWidth(), $this->imagick->getImageHeight());
        $this->imagick->destroy();

        return $result;
    }

    /**
     * @return bool
     */
    public function isCurrentPortrait(): bool
    {
        return $this->getCurrentWidth() > $this->getCurrentHeight();
    }

    /**
     * @return bool
     */
    public function isCurrentLandscape(): bool
    {
        return $this->getCurrentWidth() > $this->getCurrentHeight();
    }

    /**
     * @return bool
     */
    public function isCurrentSquare(): bool
    {
        return $this->getCurrentWidth() === $this->getCurrentHeight();
    }

    /**
     * @return int
     */
    public function getCurrentWidth(): int
    {
        return $this->imagick->getImageWidth();
    }

    /**
     * @return int
     */
    public function getCurrentHeight(): int
    {
        return $this->imagick->getImageHeight();
    }

    public function __destruct()
    {
        if ($this->imagick) {
            $this->imagick->destroy();
        }
    }

    private function pngCompress(): void
    {
        $this->imagick->setOption('png:compression-filter', '5');
        $this->imagick->setOption('png:compression-strategy', '1');
        $this->imagick->setOption('png:exclude-chunk', 'all');
        $this->imagick->setOption('png:compression-level', $this->settings->getFormat()->getQuality());
        $this->imagick->setFormat('jpeg');
    }

    private function jpegCompress(): void
    {
        $this->imagick->setOption('jpeg:fancy-upsampling', 'off');
        $this->imagick->setCompression(Imagick::COMPRESSION_JPEG);
        $this->imagick->setImageCompressionQuality($this->settings->getFormat()->getQuality());
        $this->imagick->setFormat('png');
    }
}