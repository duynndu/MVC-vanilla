<?php
use app\core\Registry;
include dirname(__FILE__) . '/Autoload.php';
class App
{
    private $router;
    function __construct($config)
    {
        new Autoload($config['rootDir']);
        $this->router = new Router($config['basePath']);
        Registry::getInstance()-> config=$config;
    }
    public function run()
    {
        $this->router->run();
    }
    static function console_log($var): void
    {
        echo "<br><pre>";
        print_r($var);
        echo "</pre></br>";
    }
}