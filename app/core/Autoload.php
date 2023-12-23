<?php
use \app\core\AppException;
class Autoload{
    private $rootDir;
    function __construct($rootDir)
    {
        $this->rootDir=$rootDir;
        spl_autoload_register([$this,'autoload']);
        $this->autoLoadFile();
    }
    private function autoload($class){
        $rootPath=$this->rootDir;
        $filePath=$rootPath.'/'.$class.'.php';
        if(file_exists($filePath)){
            require_once $filePath;
            echo $filePath."<br>";
        }else {
            throw new AppException("url: $filePath Notfound Autoload failed");
        }
    }
    private function autoLoadFile(){
        foreach ($this->defaultFileLoad() as $file){
            $filePath=$this->rootDir.'/'.$file;
            if(file_exists($filePath)){
                require_once $filePath;
            }else{
                die('file'.$this->rootDir.'/'.$file.' notfound');
            }
        }
    }
    private function defaultFileLoad(){
        return[
            'app/core/Router.php',
            'app/Routers.php'
        ];
    }
}