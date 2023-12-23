<?php

namespace app\core;

class Registry
{
    private static $instance;
    private $storage;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __set($name, $value)
    {
        $this->storage[$name] = $value;
    }

    public function __get($name)
    {
        return $this->storage[$name] ?? null;
    }
}
Registry::getInstance()->name='duynnz';
print_r(Registry::getInstance()->name);