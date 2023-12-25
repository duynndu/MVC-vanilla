<?php
namespace app\core;
class AppException extends \Exception {
    public function __construct(string $message = "", int $code = 0)
    {
        parent::__construct($message, $code);
        set_exception_handler([$this,'exception_handle']);
    }
    public function exception_handle($error){
        echo "<pre><br><hr>";
        echo "<h4 style='color: #ff4040'>{$error->getMessage()}</h4>";
        echo "<h4>>>>>File {$error->getFile()}</h4>";
        echo "<h4>>>>>Line {$error->getLine()}</h4><hr><br>";
        foreach ($error->getTrace() as $trace) {
            $file=$trace['file']??'Autoload.php';
            $function=$trace['function']??'';
            $args=$trace['args'][0]??'';
            $line=$trace['line']??'';
            echo "<h5>File :$file</h5>";
            echo "<h5>Function :$function</h5>";
            echo "<h5>Arg :</h5>";
            print_r($args);
            echo "<h5>Line :$line</h5><hr><br>";
        }
        echo "</pre>";
    }
}