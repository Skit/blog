<?php


namespace blog\entities\post\interfaces;

use blog\entities\post\Comment;
use blog\entities\post\Post;

interface CommentInterface
{
    public function hasParent(): bool;

    public function hasChild(): bool;

    public function setParent(Comment $comment): void;

    public function setChild(Comment $comment): void;

    public function createHasCode(): void;

    public function getPost(): Post;

    public function getHashCode(): ?string;
}