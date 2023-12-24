<?php
use app\core\Registry;
use \app\core\AppException;
class Router
{
    private $basePath;
    private static $routers = [];

    public function __construct($basePath)
    {
        $this->basePath=$basePath;
    }

    private function getRequestURL() //trả về đường dẫn sau public
    {
        $basePath = $this->basePath;
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        echo $url;
        $url = str_replace($basePath, '', $url);
        return empty($url) ? '/' : $url;
    }

    private function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    function run(): void
    {
        $this->map();
    }

    function map(): void
    {
        $requestURL = $this->getRequestURL();
        $requestMethod = $this->getRequestMethod();
        $params = [];
        $routers = self::$routers;
        foreach ($routers as $router) {
            list($method, $url, $action) = $router;
            if ($url === '/') {
                $url = '/home';
            }
            if ($requestURL === '/') {
                $requestURL = '/home';
            }
            if (preg_match('/^\/{\w+}/', $url)) {
                $url = substr_replace($url, 'home/', 1, 0);
            }
            $requestUrlArr = explode('/', $requestURL);
            if ($this->issetUrl($requestUrlArr[1]) === false) {
                if (!str_contains($requestURL, 'home')) {
                    $requestURL = substr_replace($requestURL, 'home/', 1, 0);
                }
            }
            $urlArr = explode('/', $url);
            $requestUrlArr = explode('/', $requestURL);

            if (str_contains($method, $requestMethod)) {
                if (count($urlArr) == count($requestUrlArr)) {
                    if ($requestUrlArr[1] == $urlArr[1]) {
                        $params = $this->handleParams($urlArr, $requestUrlArr);
                        if (is_callable($action)) {
                            call_user_func_array($action, array_values($params));
                            break;
                        }
                        if (is_string($action)) {
                            $this->compileParams($action, $params);
                            break;
                        }
                    }
                }
                if ($url === '*') {
                    $action();
                    break;
                }
            }
        }
    }

    private function compileParams($action, $params): void
    {
        $action = explode('@', $action);
        $className = 'app\\controllers\\' . $action[0];
        $methodName = $action[1];
        if (class_exists($className)) {
            $obj = new $className;
            Registry::getInstance()->controller=$action[0];
            if (method_exists($className, $methodName)) {
                call_user_func_array([$obj, $methodName], array_values($params));
                Registry::getInstance()->method=$methodName;
            } else {
                throw new AppException("Method $methodName not found");
            }
        } else {
            throw new AppException("Class $className not found");
        }
    }

    private function issetUrl($requestUrl)
    {
        foreach (self::$routers as $router) {
            list($method, $url, $action) = $router;
            $urlArr = explode('/', $url);
            if (isset($urlArr[1])) {
                if ($requestUrl === $urlArr[1]) {
                    return true;
                }
            }
        }
        return false;
    }

    private function handleParams($urlArr, $requestUrlArr): array
    {
        $params = array_combine($urlArr, $requestUrlArr);
        $params = array_filter($params, function ($key) {
            return preg_match('/^{\w+}$/', $key);
        }, ARRAY_FILTER_USE_KEY);
        return $params;
    }


    private static function addRouter($method, $url, $action): void
    {
        self::$routers[] = [$method, $url, $action];
    }

    static function get($url, $action): void
    {
        self::addRouter('GET', $url, $action);
    }

    static function post($url, $action): void
    {
        self::addRouter('POST', $url, $action);
    }

    static function any($url, $action): void
    {
        self::addRouter('GET|POST', $url, $action);
    }
}