<?php


namespace blog\components\ImageResizer\interfaces;


interface FormatInterface
{
    public function getQuality(): int;

    public function getExtension(): string ;
}