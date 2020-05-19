<?php

use blog\entities\tag\Tag;
use blog\entities\tag\TagBundle;
use Codeception\Specify;
use Codeception\Stub;
use Codeception\Test\Unit;

/**
 * TODO починить тесты
 * Class TagBundleTest
 */
class TagBundleTest extends Unit
{
    use Specify;

    public function testBundle()
    {
        $tags = [
            [
                'id' => 1,
                'title' => '1 title',
                'slug' => '1 title',
                'status' => 1,
            ],
            [
                'id' => 2,
                'title' => '2 title',
                'slug' => '2 title',
                'status' => 1,
            ],
        ];

        $bundle = new TagBundle($tags, function ($item) {
            return Stub::make(Tag::class, ['id' => $item['id']]);
        });

        $this->specify('Check bundle', function () use ($bundle) {
            expect($bundle->getBundle())->notEmpty();
            expect($bundle->getCount())->equals(2);
        });

        $this->specify('Remove from bundle', function () use ($bundle) {
            expect($bundle->removeByPrimaryKey(2))->true();
            expect($bundle->getCount())->equals(1);
            expect($bundle->findByPrimaryKey(2))->null();
            expect($bundle->findByPrimaryKey(1))->isInstanceOf(Tag::class);
        });
    }

    public function testWithEmptyTag()
    {
        $bundle = new TagBundle([], function () {});

        expect($bundle->getBundle())->isEmpty();
        expect($bundle->getCount())->equals(0);
    }
}