<?php declare(strict_types=1);

namespace blog\entities\common;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * TODO написать тесты
 * Class Date
 * @package blog\entities\common
 */
class Date
{
    public const RETURN_FORMAT = 'Y-m-d H:i:s';
    public const DEFAULT_TIMEZONE = 'Europe/Moscow';

    private $date;

    /**
     * @return string
     */
    public static function getFormatNow(): string
    {
        return date(static::RETURN_FORMAT);
    }

    /**
     * Date constructor.
     * @param string $date
     * @param string $timeZone
     * @throws Exception
     */
    public function __construct(string $date = null, string $timeZone = null)
    {
        $this->date = new DateTime($date ?? date(static::RETURN_FORMAT));
        $this->date->setTimezone(new DateTimeZone($timeZone ?? static::DEFAULT_TIMEZONE));
    }

    public function getFormatted(): string
    {
        return $this->date->format(static::RETURN_FORMAT);
    }

    public function getTimestamp(): int
    {
        return $this->date->getTimestamp();
    }
}