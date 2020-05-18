<?php


namespace blog\components\PathReplacer;

/**
 * Class PathReplacer
 * @package blog\components\PathReplacer
 */
class PathReplacer
{
    private $skipped = [];
    private $rootVariables;
    private $localVariables;
    private $globalVariables = [];

    /**
     * PathReplacer constructor.
     * @param string $rootDir
     * @param NS[] $namespaces
     */
    public function __construct(string $rootDir, NS ...$namespaces)
    {
        $this->rootVariables = new NS('root', ['rootDir' => $rootDir]);
        $this->globalVariables = $namespaces;
    }

    /**
     * @param array $vars
     * @return PathReplacer
     */
    public function setVars(array $vars): self
    {
        $this->localVariables = new NS('local', $vars);

        return $this;
    }

    /**
     * @param string $path
     * @param bool $throwIfSkipped
     * @return string
     * @throws PathReplacerExceptions
     */
    public function replace(string $path, bool $throwIfSkipped = true): string
    {
        if ($path) {
            $pathNS = $this->getPathNamespace($path);
            $path = str_replace("{$pathNS->getName()}:", '', $path);

            while (preg_match('~{(\w+)}~', $path, $m)) {
                if ($var = $pathNS->{$m[1]} ?: $this->localVariables->{$m[1]} ?? $this->rootVariables->{$m[1]}) {
                    $path = str_replace($m[0], $var, $path);
                } else {
                    if ($throwIfSkipped) {
                        throw new PathReplacerExceptions("Unknown variable: {$m[0]}");
                    }

                    $this->skipped($m[0]);
                    $path = str_replace($m[0], '', $path);
                }
            }
        }

        return $path;
    }

    /**
     * @return bool
     */
    public function hasSkipped(): bool
    {
        return !empty($this->skipped);
    }

    /**
     * @return array
     */
    public function getSkippedVariables(): array
    {
        return $this->skipped;
    }

    /**
     * @param string $path
     * @return NS
     * @throws PathReplacerExceptions
     */
    private function getPathNamespace(string $path): NS
    {
        if (preg_match('~(?<ns>\w+):~', $path, $m)) {
            foreach ($this->globalVariables as $ns) {
                if ($ns->getName() === $m['ns']) {
                    return $ns;
                }
            }
        }

        throw new PathReplacerExceptions("Incorrect namespace: {$m['ns']}");
    }

    /**
     * @param string $var
     */
    private function skipped(string $var)
    {
        $this->skipped[] = $var;
    }
}