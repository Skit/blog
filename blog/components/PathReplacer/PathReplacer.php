<?php declare(strict_types=1);


namespace blog\components\PathReplacer;

/**
 * Class PathReplacer
 * @property NS $currentNS
 * @property NS $localNS
 * @property NS $unpackNS
 * @property NS $rootNS
 * @property NS[] $globalNS
 *
 * @package blog\components\PathReplacer
 */
class PathReplacer
{
    private $path;
    private $currentNS;
    private $rootNS;
    private $localNS;
    private $unpackNS;
    private $skipped = [];
    private $globalNS = [];
    private $unpackVariables = [];

    /**
     * PathReplacer constructor.
     * @param string $rootDir
     * @param NS[] $namespaces
     */
    public function __construct(string $rootDir, NS ...$namespaces)
    {
        $this->rootNS = new NS('root', [
            'rootDir' => $rootDir,
            'year' => date('Y')
        ]);

        $this->globalNS = $namespaces;
        $this->currentNS = $this->rootNS;
    }

    /**
     * TODO важна последовательность сперва setVars потом replace - переделать, чтобы последовательность роли не играла
     * @param string $path
     * @param bool $throwIfSkipped
     * @return PathReplacer
     * @throws PathReplacerExceptions
     */
    public function replace(string $path, bool $throwIfSkipped = true): self
    {
        if ($path) {
            $this->setPathNamespace($path);
            $path = str_replace("{$this->currentNS->getName()}:", '', $path);

            while (preg_match('~{(\w+)}~', $path, $m)) {
                if ($var = $this->currentNS->{$m[1]} ?: $this->localNS->{$m[1]} ?? $this->rootNS->{$m[1]}) {
                    $path = str_replace($m[0], $var, $path);
                    $this->unpackVariables($m[0], $m[1], $var);
                } else {
                    if ($throwIfSkipped) {
                        throw new PathReplacerExceptions("Unknown variable: {$m[0]}");
                    }

                    $this->skipped($m[0]);
                    $path = str_replace($m[0], '', $path);
                }
            }
        }

        $this->path = $path;
        $this->sentUnpackNS();

        return $this;
    }

    /**
     * @return string
     */
    public function existIncrement(): string
    {
        if (is_dir($this->path) || file_exists($this->path)) {
            $pathInfo = pathinfo($this->path);

            if (preg_match('~_(?<c>\d+)~', $pathInfo['filename'], $m)) {
                $incremented = preg_replace('~_\d+$~', '_' . ((int) $m['c'] + 1), $pathInfo['filename']);
            } else {
                $incremented = "{$pathInfo['filename']}_1";
            }

            $incremented = $incremented . (!empty($pathInfo['extension']) ? ".{$pathInfo['extension']}" : '');
            $this->path = preg_replace("~{$pathInfo['basename']}$~", $incremented, $this->path);

            return $this->existIncrement();
        }

        return $this->path;
    }

    /**
     * Replace {public} variable to {domain}
     * From (/var/www/html/uploads/file.txt) to (http://domain.com/uploads/file.txt)
     *
     * @return string
     * @throws PathReplacerExceptions
     */
    public function getUrl(): string
    {
        if (!$this->currentNS->domain || !$this->unpackNS->public) {
            throw new PathReplacerExceptions('Variable {domain} and {public} must be set');
        }

        return str_replace($this->unpackNS->public, $this->currentNS->domain, $this->getPath());
    }

    /**
     * Public is a path to shared folder that will be replace
     * Public (/var/www/html), whole path (/var/www/html/uploads/images) -> uploads/images
     *
     * @return string
     * @throws PathReplacerExceptions
     */
    public function getRelative(): string
    {
        if (!$this->unpackNS->public) {
            throw new PathReplacerExceptions('Variable {public} must be set');
        }

        return str_replace($this->unpackNS->public . '/', '', $this->getPath());
    }

    /**
     * Absolute replaced path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getSkippedVariables(): array
    {
        return $this->skipped;
    }

    /**
     * @param array $vars
     * @return PathReplacer
     */
    public function setVars(array $vars): self
    {
        $this->localNS = new NS('local', $vars);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSkipped(): bool
    {
        return !empty($this->skipped);
    }

    /**
     * @param string $path
     * @return void
     * @throws PathReplacerExceptions
     */
    private function setPathNamespace(string $path): void
    {
        if (preg_match('~(?<ns>\w+):~', $path, $m)) {
            foreach ($this->globalNS as $ns) {
                if ($ns->getName() === $m['ns']) {
                    $this->currentNS = $ns;

                    return;
                }
            }

            throw new PathReplacerExceptions("Incorrect namespace: {$m['ns']}");
        }
    }

    /**
     * @param string $var
     * @param string $varName
     * @param string $val
     */
    private function unpackVariables(string $var, string $varName, string $val): void
    {
        $this->unpackVariables = array_map(function ($item) use ($var, $varName, $val) {
            return str_replace($var, $val, $item);
        }, $this->unpackVariables += [$varName => $val]);
    }

    /**
     * @inheritDoc
     */
    private function sentUnpackNS(): void
    {
        $this->unpackNS = new NS('unpack', $this->unpackVariables);
        $this->unpackVariables = [];
    }

    /**
     * @param string $var
     */
    private function skipped(string $var): void
    {
        $this->skipped[] = $var;
    }
}