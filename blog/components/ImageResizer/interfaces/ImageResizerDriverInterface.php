<?php


namespace blog\components\ImageResizer\interfaces;

use blog\components\ImageResizer\entities\Path;
use blog\components\ImageResizer\entities\Result;
use blog\components\ImageResizer\entities\Size;

/**
 * Interface ImageResizerDriverInterface
 * @package blog\components\ImageResizer\interfaces
 */
interface ImageResizerDriverInterface
{
    public function getCurrentWidth(): int;

    public function getCurrentHeight(): int;

    public function getTargetExtension(): string;

    public function isCurrentSquare(): bool;

    public function isCurrentPortrait(): bool;

    public function isCurrentLandscape(): bool;

    public function strip(): ImageResizerDriverInterface;

    public function compress(bool $skip = false): ImageResizerDriverInterface;

    public function sharp(): ImageResizerDriverInterface;

    public function modulate(): ImageResizerDriverInterface;

    public function postProcessing(): ImageResizerDriverInterface;

    public function save(Path $path, bool $makePath = true): Result;

    public function marginalResize(Size $size): ImageResizerDriverInterface;

    public function resize(Size $size): ImageResizerDriverInterface;

    public function adaptiveResize(Size $size): ImageResizerDriverInterface;

    public function crop(Size $size): ImageResizerDriverInterface;

    public function create(Path $path): ImageResizerDriverInterface;

    public function __construct(ImageResizerSettingsInterface $settings);
}