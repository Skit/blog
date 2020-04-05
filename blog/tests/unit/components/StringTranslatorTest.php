<?php


namespace blog\tests\unit\components;

use blog\components\StringTranslator\drivers\interfaces\StringTranslateDriverInterface;
use blog\components\StringTranslator\drivers\MyMemoryDriver;
use blog\components\StringTranslator\drivers\OfflineDriver;
use blog\components\StringTranslator\drivers\YandexDriver;
use blog\components\StringTranslator\exceptions\StringTranslatorException;
use blog\components\StringTranslator\StringTranslator;
use Codeception\Specify;
use Codeception\Stub;
use Codeception\Test\Unit;

/**
 * Class OnlineTranslatorTest
 * @package blog\tests\unit\components
 */
class StringTranslatorTest extends Unit
{
    use Specify;

    /**
     * @var StringTranslateDriverInterface
     */
    private $driver;

    public function testDriversWithEmptyText()
    {
        verify(StringTranslator::translate(new OfflineDriver('')))->equals('');
        verify(StringTranslator::translate(new YandexDriver('')))->equals('');
        verify(StringTranslator::translate(new MyMemoryDriver('')))->equals('');
    }

    public function testDriversSuccessful()
    {
        $this->specify('Offline translate', function() {
            verify(StringTranslator::translate(new OfflineDriver('Русский')))->equals('Russkiy');
        });

        $this->specify('Yandex online 200 OK', function() {
            $this->driver = Stub::construct(YandexDriver::class, ['text' => 'Русский'], ['getServerResponse' => (object) [
                'text' => ['Russian'],
                'code' => 200
            ]]);

            verify(StringTranslator::translate($this->driver))->equals('Russian');
        });

        $this->specify('MyMemory online 200 OK', function() {
            $this->driver = Stub::construct(MyMemoryDriver::class, ['text' => 'Русский'], ['getServerResponse' => (object) [
                'responseData' => (object) ['translatedText' => 'Russian'],
                'responseStatus' => 200
            ]]);

            verify(StringTranslator::translate($this->driver))->equals('Russian');
        });
    }

    public function testDriversWrongResponse()
    {
        $this->specify('Yandex online 500', function() {
            $this->driver = Stub::construct(YandexDriver::class, ['text' => 'Русский'], ['getServerResponse' => (object) [
                'text' => ['Russian'],
                'message' => 'Some error',
                'code' => 500
            ]]);

            $this->expectException(StringTranslatorException::class);
            StringTranslator::translate($this->driver);
        });

        $this->specify('MyMemory online 500', function() {
            $this->driver = Stub::construct(MyMemoryDriver::class, ['text' => 'Русский'], ['getServerResponse' => (object) [
                'responseData' => (object) ['translatedText' => 'Some error'],
                'responseStatus' => 400
            ]]);

            $this->expectExceptionMessage('Some error');
            StringTranslator::translate($this->driver);
        });
    }
}