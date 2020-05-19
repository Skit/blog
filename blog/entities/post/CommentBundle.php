<?php declare(strict_types=1);

namespace blog\entities\post;

use blog\entities\common\exceptions\BundleExteption;
use blog\entities\common\RecursiveContentBundle;

/**
 * Class CommentBundle
 * @property Comment[] $bundle
 * @package blog\entities\post
 */
class CommentBundle extends RecursiveContentBundle
{
    /**
     * TagBundle constructor.
     * @param array $comments
     * @throws BundleExteption
     * @throws exceptions\CommentException
     */
    public function __construct(array $comments)
    {
        try {
            parent::__construct($this->createFromArray($comments));
        } catch (BundleExteption $e) {
            throw new BundleExteption("Fail to create comment bundle: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @param int $pk
     * @return bool
     */
    public function removeByPrimaryKey(int $pk): ?bool
    {
        if ($item = $this->findByPrimaryKey($pk)) {
            $item->delete();
            $this->count--;

            return true;
        }

        return null;
    }

    /**
     * @param array $comments
     * @return array
     * @throws exceptions\CommentException
     */
    private function createFromArray(array $comments): array
    {
        // Create pull of all object
        $result = $allObject = [];
        foreach ($comments as $item) {
            $allObject += [
                $item['id'] => Comment::construct(
                    $item['id'],
                    new Text($item['text']),
                    $item['post'],
                    $item['creator'],
                    null,
                    (new Dates())->asNew(),
                    $item['status']
                )
            ];

            $this->count++;
        }

        // Set parent if created comment has it
        foreach ($comments as $item) {
            if ($item['parentComment']) {
                $child = $allObject[$item['id']];
                $child->putIn($allObject[$item['parentComment']]);
            } else {
                $result[] = $allObject[$item['id']];
            }
        }

        return $result;
    }
}