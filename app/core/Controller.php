<?php
namespace app\core;
class Controller{
    private $layout=null;
    public function __construct()
    {
        $this->layout=Registry::getInstance()-> config['layout'];
    }
    public function setLayout($layout){
        $this->layout=$layout;
    }
    public function redirect($url,$isEnd=false,$responseCode=302){
        header("location: $url");
        if($isEnd){
            die;
        }
    }
    public function render($view,$data=null){
        $rootDir=Registry::getInstance()-> config['rootDir'];
        $layoutPath="$rootDir/app/views/$this->layout.php";
        $content=$this->getContent($view,$data);
        if(is_array($data)){
            extract($data);
        }
        if(file_exists($layoutPath)){
            include $layoutPath;
        }else{
            echo "file $view không tồn tại";
        }
    }
    public function getContent($view,$data)
    {
        $rootDir=Registry::getInstance()-> config['rootDir'];
        $folder=strtolower(str_replace('Controller','',Registry::getInstance()->controller));
        $urlFile="$rootDir/app/views/$folder/$view.php";
        if(is_array($data)){
            extract($data);
        }
        if(file_exists($urlFile)){
            ob_start();
            include $urlFile;
        }else{
            echo "file $urlFile không tồn tại";
        }
        return ob_get_clean();
    }
    public function renderPartial($view,$data){
        $rootDir=Registry::getInstance()-> config['rootDir'];
        $urlFile="$rootDir/app/views/$view.php";
        if(is_array($data)){
            extract($data);
        }
        if(file_exists($urlFile)){
            include $urlFile;
        }else{
            echo "file $view không tồn tại";
        }
    }
}