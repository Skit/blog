<?php


namespace blog\entities\post\interfaces;

use blog\entities\post\Comment;

interface CommentInterface
{
    public function hasParent(): bool;

    public function hasChild(): bool;

    public function setParent(Comment $comment): void;

    public function setChild(Comment $comment): void;

    public function createHasCode(): void;

    public function getHashCode(): string;
}