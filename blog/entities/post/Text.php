<?php


namespace blog\entities\post;


use blog\entities\post\exceptions\CommentException;

/**
 * Class Text
 * @package blog\entities\post
 */
class Text
{
    private $text;

    /**
     * Text constructor.
     * @param string $text
     * @throws CommentException
     */
    public function __construct(string $text)
    {
        $this->validate($text);
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->text;
    }

    /**
     * @param string $content
     * @return void
     * @throws CommentException
     */
    private function validate(string $content): void
    {
        // TODO вынести из объекта число символов
        if (mb_strlen($content) > 300) {
            throw new CommentException("Maximum number of characters is: 300");
        }

        if (preg_match_all('~((?:https?)?(?:[\S]{3,}\.[\S]{2,}))~', $content, $match)) {
            // TODO вынести из объекта число ссылок
            if (count($match[0]) > 5) {
                throw new CommentException('Your comment looks like SPAM');
            }
        }
    }
}