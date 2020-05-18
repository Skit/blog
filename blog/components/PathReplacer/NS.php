<?php


namespace blog\components\PathReplacer;


/**
 * Class NS
 * @package blog\tests\unit\components
 */
class NS {

    private $name;
    private $variables;

    /**
     * NS constructor.
     * @param string $namespace
     * @param array $variables
     */
    public function __construct(string $namespace, array $variables = [])
    {
        $this->name = $namespace;
        $this->variables = $variables;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return string
     */
    public function __get($name): ?string
    {
        return $this->variables[$name] ?? null;
    }
}