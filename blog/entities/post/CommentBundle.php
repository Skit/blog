<?php declare(strict_types=1);

namespace blog\entities\post;

use blog\entities\common\RecursiveContentBundle;

/**
 * Class CommentBundle
 * @property Comment[] $bundle
 * @package blog\entities\post
 */
class CommentBundle extends RecursiveContentBundle
{
    /**
     * CommentBundle constructor.
     * @param array $comments
     * @throws exceptions\CommentException
     */
    public function __construct(array $comments)
    {
        parent::__construct($this->createFromArray($comments));
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
     * @deprecated
     * @param array $comments
     * @return array
     * @throws exceptions\CommentException
     */
    private function createFromArray(array $comments): array
    {
        $result = $allObject = [];
        foreach ($comments as $item) {
            $allObject += [
                $item['id'] => Comment::create($item['text'], $item['creator'], null, $item['status'])
            ];
            $this->count++;
        }

        foreach ($comments as $item) {
            if ($item['parentComment']) {
                $child = $allObject[$item['id']];
                $child->edit($child->getContent(), $allObject[$item['parentComment']], $child->getStatus());
            } else {
                $result[] = $allObject[$item['id']];
            }
        }

        return $result;
    }
}