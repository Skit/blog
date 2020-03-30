<?php

use blog\entities\tag\Tag;
use Codeception\Stub;
use Codeception\Test\Unit;

class TagTest extends Unit
{

    public function testCreate()
    {
        $tag = Tag::create(1, 'Title', 'Slug', Tag::STATUS_ACTIVE);

        expect($tag->getTitle())->equals('Title');
        expect($tag->getSlug())->equals('Slug');
        expect($tag->isActive())->true();
    }

    public function testActivate()
    {
        $tag = Stub::make(Tag::class, ['status' => Tag::STATUS_INACTIVE]);

        $tag->activate();
        expect($tag->isActive())->true();
    }

    public function testWrongActivate()
    {
        $tag = Stub::make(Tag::class, ['status' => Tag::STATUS_ACTIVE]);

        $this->expectExceptionMessage('Record is already active');
        $tag->activate();
    }

    public function testWrongDeactivate()
    {
        $tag = Stub::make(Tag::class, ['status' => Tag::STATUS_INACTIVE]);

        $this->expectExceptionMessage('Record is already inactive');
        $tag->deactivate();
    }

    public function testEdit()
    {
        $tag = Stub::make(Tag::class, ['status' => Tag::STATUS_INACTIVE]);
        $tag->edit('Edit title', 'Edit slug', Tag::STATUS_ACTIVE);

        expect($tag->getTitle())->equals('Edit title');
        expect($tag->getSlug())->equals('Edit slug');
        expect($tag->isActive())->true();
    }
}