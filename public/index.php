<?php
use app\core\Registry;
include dirname(__FILE__) . '/../app/core/App.php';
$config=include dirname(__FILE__) . '/../config/main.php';
$app = new App($config);
Registry::getInstance()->cofig=include dirname(__FILE__) . '/../config/main.php';
$app->run();