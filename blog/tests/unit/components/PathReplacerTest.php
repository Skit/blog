<?php


namespace blog\tests\unit\components;

use blog\components\PathReplacer\NS;
use blog\components\PathReplacer\PathReplacer;
use Codeception\Specify;
use Codeception\Test\Unit;

/**
 * Class PathReplacerTest
 * @package blog\tests\unit\components
 */
class PathReplacerTest extends Unit
{
    use Specify;

    public function testVariable()
    {
        $replacer = new PathReplacer('/var/www',
            new NS('front', ['uploads' => '{rootDir}/frontend/web/uploads', 'imageExt' => 'jpg']),
            new NS('back', ['uploads' => '{rootDir}/backend/web/uploads'])
        );

        $this->specify('Use front namespace', function() use ($replacer) {
            $path = 'front:{uploads}/posts/{id}/images/{filename}.{imageExt}';
            $replacer->setVars(['id' => '123', 'filename' => 'new']);

           verify($replacer->replace($path))->equals('/var/www/frontend/web/uploads/posts/123/images/new.jpg');
        });

        $this->specify('Use back namespace', function() use ($replacer) {
            $path = 'back:{uploads}/posts/{id}/images/{filename}.{ext}';
            $replacer->setVars(['id' => '123', 'filename' => 'new', 'ext' => 'jpg']);

            verify($replacer->replace($path))->equals('/var/www/backend/web/uploads/posts/123/images/new.jpg');
        });

        $this->specify('Use wrong namespace', function() use ($replacer) {
            $path = 'foo:{uploads}/posts/{id}/images/{filename}.{ext}';

            $this->expectExceptionMessage('Incorrect namespace: foo');
            $replacer->replace($path);
        });

        $this->specify('Set wrong variables without exception', function() use ($replacer) {
            $this->expectExceptionMessage('Unknown variable: {none1}');
            $replacer->replace('back:{uploads}/posts/{none1}/images/{none2}.{none3}');
        });

        $this->specify('Set wrong variables without exception', function() use ($replacer) {
            $path = $replacer->replace('back:{uploads}/posts/{none1}/images/{none2}.{none3}', false);

            verify($replacer->hasSkipped())->true();
            verify($replacer->getSkippedVariables())->count(3);
            verify($path)->equals('/var/www/backend/web/uploads/posts//images/.');
        });

        $this->specify('Set empty path', function() use ($replacer) {
            verify($replacer->replace(''))->equals('');
        });

        $this->specify('Set empty path with namespace', function() use ($replacer) {
            verify($replacer->replace('back:'))->equals('');
        });
    }
}