<?php
namespace app\controllers;
use app\core\Controller;
use App;
use DB;
class HomeController extends Controller {
    function __construct()
    {
        parent::__construct();
    }
    function index(){
        echo 'đây là trang home của class Home <br>';
        App::console_log(func_get_args());
        $this->render('index','this is home page');
        DB::table('Cart')->select('*')->get();
    }
}