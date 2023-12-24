<?php
namespace app\core;
class AppException extends \Exception {
    public function __construct(string $message = "", int $code = 0)
    {
        parent::__construct($message, $code);
        set_exception_handler([$this,'exception_handle']);
    }
    public function exception_handle($error){
        echo "<pre>";
        echo "<h1 style='color: #ff4040'>{$error->getMessage()}</h1>";
        echo "<h2>>>>>File {$error->getFile()}</h2>";
        echo "<h2>>>>>Line {$error->getLine()}</h2>";
        foreach ($error->getTrace() as $trace) {
            $file=$trace['file']??'Autoload.php';
            $function=$trace['function']??'';
            $args=$trace['args'][0]??'';
            $line=$trace['line']??'';
            echo "<h3>File :$file</h3>";
            echo "<h3>Function :$function</h3>";
            echo "<h3>Arg :</h3>";
            print_r($args);
            echo "<h3>Line :$line</h3>";
            echo "<hr><br>";
        }
    }
}