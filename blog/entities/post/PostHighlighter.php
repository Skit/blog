<?php


namespace blog\entities\post;


use blog\entities\post\interfaces\HighlighterInterface;
use Highlight\Highlighter;
use stdClass;

/**
 * Class Highlighter
 * @package blog\entities\post
 */
class PostHighlighter implements HighlighterInterface
{
    private $h1;
    private $pattern = '~\<(\w+)\s+\w+=[\\"\\\'](?:[^\\"\\\']+)?_lang__(\w+)(?:\s+)?[^\\"\\\']*[\\"\\\'](?:\s*)\>(.+)\<\/\1\>~s';
    private $content = '';
    private $result = 0;

    /**
     * PostHighlighter constructor.
     */
    public function __construct()
    {
        $this->h1 = new Highlighter();

        return $this;
    }

    /**
     * @return string
     */
    public function getHighlighted(): ?string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isHighlighted(): bool
    {
        return $this->result > 0;
    }

    /**
     * @param string $content
     * @return HighlighterInterface
     */
    public function highlighting(string $content): HighlighterInterface
    {
        try {
            if ($count = preg_match_all($this->pattern, $content, $matches)) {
                for ($i = 0; $i < $count; $i++) {
                    $highlighted = $this->h1->highlight($matches[2][$i], $matches[3][$i]);
                    $this->result += $highlighted->relevance;
                    $replace = $this->codeBlockTmpl($highlighted);
                    $content = str_replace($matches[0][$i], $replace, $content);
                }
            }
        } catch (\Exception $e) {
            // TODO подумать, лог или какое-то сообщение на форму
            // If language does not support
        } finally {
            $this->content = $content;
        }

        return $this;
    }

    /**
     * @param stdClass $result
     * @return string
     */
    private function codeBlockTmpl(stdClass $result)
    {
        $tmpl = "<pre><code class=\"hljs {$result->language}\">";
        $tmpl .= $result->value;
        $tmpl .= "</code></pre>";

        return $tmpl;
    }
}