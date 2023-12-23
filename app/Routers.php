<?php
Router::get('/', 'HomeController@index');
Router::get('/{page}', 'HomeController@index');
Router::any('/news', function () {
    echo 'đây là trang tin tức';
});
Router::any('/products', function () {
    echo 'đây là trang sản phẩm';
});
Router::any('/products/{category}/{page}', function () {
    echo 'đây là trang sản phẩm </br>';
    print_r(func_get_args());
});
Router::get('/news/{page}', 'NewController@index');

//notfound
Router::any('*', function () {
    echo '<h1 align="center">404 Not Found</h1>';
});