<?php

namespace iutnc\deefy\loader;

class Psr4ClassLoader {
    private string $prefix;
    private string $suffix;

    public function __construct(string $prefix, string $suffix) {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    public function loadClass(string $className): void {
        if (strpos($className, $this->prefix) !== 0) {
            return;
        }
        $reste = substr($className, strlen($this->prefix));
        $changement = $this->suffix . str_replace('\\', '/', $reste) . '.php';
        if (is_file($changement)) {
            require_once $changement;
        }
    }

    public function register(): void {
        spl_autoload_register([$this, 'loadClass']);
    }
}
