<?php declare(strict_types=1);

namespace blog\entities\tag;


use blog\entities\common\abstracts\bundles\ObjectBundle;
use blog\entities\common\exceptions\BundleExteption;
use blog\entities\tag\exceptions\TagException;
use Closure;

/**
 * TODO поменять исключение на BundleException
 * Class TagBundle
 *
 * @property Tag[] $bundle
 * @package blog\entities\tag
 */
class TagBundle extends ObjectBundle
{
    /**
     * TagBundle constructor.
     * @param array $tags
     * @param Closure|null $closure
     * @throws TagException
     */
    public function __construct(array $tags, Closure $closure)
    {
        try {
            parent::__construct($tags, $closure);
        } catch (BundleExteption $e) {
            throw new TagException("Fail to create tag bundle: {$e->getMessage()}", 0, $e);
        }
    }
}