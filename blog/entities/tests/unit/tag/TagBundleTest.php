<?php

use blog\entities\tag\Tag;
use blog\entities\tag\TagBundle;
use Codeception\Test\Unit;

/**
 * Class TagBundleTest
 */
class TagBundleTest extends Unit
{
    public function testCreate()
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

        $bundle = new TagBundle($tags);

        expect($bundle->getBundle())->notEmpty();
        expect($bundle->getCount())->equals(2);
    }

    public function testWithEmptyTag()
    {
        $bundle = new TagBundle([]);

        expect($bundle->getBundle())->isEmpty();
        expect($bundle->getCount())->equals(0);
    }

    public function testRemoveFromBundle()
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

        $bundle = new TagBundle($tags);
        $bundle->removeByPrimaryKey(2);

        expect($bundle->getCount())->equals(1);
        expect($bundle->findByPrimaryKey(2))->null();
        expect($bundle->findByPrimaryKey(1))->isInstanceOf(Tag::class);
    }
}