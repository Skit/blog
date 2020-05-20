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

           verify($replacer->replace($path)->get())->equals('/var/www/frontend/web/uploads/posts/123/images/new.jpg');
        });

        $this->specify('Use back namespace', function() use ($replacer) {
            $path = 'back:{uploads}/posts/{id}/images/{filename}.{ext}';
            $replacer->setVars(['id' => '123', 'filename' => 'new', 'ext' => 'jpg']);

            verify($replacer->replace($path)->get())->equals('/var/www/backend/web/uploads/posts/123/images/new.jpg');
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

    public function testIfExist()
    {
        $replacer = new PathReplacer('/var/www',
            new NS('test', [
                'output' => '{rootDir}/blog/tests/_output',
                'components' => '{rootDir}/blog/tests/unit/components'
            ])
        );

        $this->specify('Doesnt exist file', function() use ($replacer) {
            $replacer = $replacer->replace('test:{components}/SomeClass.java');
            expect($replacer->get())->stringContainsString($replacer->existIncrement());
        });

        $this->specify('Doesnt exist dire', function() use ($replacer) {
            $replacer = $replacer->replace('test:{components}/AkaFolder');
            expect($replacer->get())->stringContainsString($replacer->existIncrement());
        });

        $this->specify('Increment exist file', function() use ($replacer) {
            $incremented = $replacer->replace('test:{components}/PathReplacerTest.php')->existIncrement();
            expect($incremented)->stringContainsString('PathReplacerTest_1.php');
        });

        $this->specify('Increment exist dir', function() use ($replacer) {
            $incremented = $replacer->replace('test:{components}')->existIncrement();
            expect($incremented)->stringContainsString('components_1');
        });

        $this->specify('Crush test file', function() use ($replacer) {
            for ($i = 0; $i != 10; $i++) {
                $incremented = $replacer->replace('test:{output}/increment.txt')->existIncrement();
                file_put_contents($incremented, '');
            }

            $pattern = $replacer->replace('test:{output}/increment*.txt')->get();
            foreach (glob($pattern) as $file) {
                $i--;
                expect(unlink($file))->true();
            }

            expect($i)->equals(0);
        });

        $this->specify('Crush test folder', function() use ($replacer) {
            for ($i = 0; $i != 10; $i++) {
                $incremented = $replacer->replace('test:{output}/incrementDir')->existIncrement();
                mkdir($incremented);
            }

            $pattern = $replacer->replace('test:{output}/incrementDir*')->get();
            foreach (glob($pattern) as $dir) {
                $i--;
                expect(rmdir($dir))->true();
            }

            expect($i)->equals(0);
        });
    }
}