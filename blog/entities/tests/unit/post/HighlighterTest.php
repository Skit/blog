<?php


namespace blog\entities\tests\unit\post;


use blog\entities\post\PostHighlighter;
use Codeception\Test\Unit;

/**
 * Class HighlighterTest
 * @package blog\entities\tests\unit\post
 */
class HighlighterTest extends Unit
{
    public function testHighlightContent()
    {
        $content = file_get_contents(codecept_data_dir('highlightContent.html'));
        $highlighter = (new PostHighlighter())->highlighting($content);

        expect($highlighter->isHighlighted())->true();
        expect(strlen($highlighter->getHighlighted()))->greaterThan(strlen($content));
        expect($highlighter->getHighlighted())->stringContainsString('<pre><code class="hljs php">');
        expect($highlighter->getHighlighted())->stringContainsString('<pre><code class="hljs java">');
    }

    public function testNoHighlightContent()
    {
        $content = file_get_contents(codecept_data_dir('noHighlightContent.html'));
        $highlighter = (new PostHighlighter())->highlighting($content);

        expect($highlighter->isHighlighted())->false();
        expect(strlen($highlighter->getHighlighted()))->equals(strlen($content));
    }

    public function testWrongLangHighlightContent()
    {
        $content = file_get_contents(codecept_data_dir('wrongLangCodeHighlightContent.html'));
        $highlighter = (new PostHighlighter())->highlighting($content);

        expect($highlighter->isHighlighted())->false();
        expect(strlen($highlighter->getHighlighted()))->equals(strlen($content));
    }
}