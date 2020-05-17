<?php


namespace blog\entities\post\interfaces;

/**
 * Interface HighlightInterface
 * @package blog\entities\post\interfaces
 */
interface HighlighterInterface
{
    public function highlighting(string $content): self;

    public function getHighlighted(): ?string;

    public function isHighlighted(): bool;
}