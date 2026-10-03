<?php
declare(strict_types=1);

/**
 * نقطة الدخول الرئيسية — Cms-shop
 */
require __DIR__ . '/app/bootstrap.php';

use App\Core\Router;

$router = new Router();

/* ---------- واجهة المتجر ---------- */
$router->get('/', 'App\Controllers\HomeController@index');
$router->get('/shop', 'App\Controllers\CatalogController@index');
$router->get('/product/{id}', 'App\Controllers\CatalogController@product');
$router->get('/cart', 'App\Controllers\CartController@index');
$router->post('/cart/add', 'App\Controllers\CartController@add');
$router->post('/cart/update', 'App\Controllers\CartController@update');
$router->post('/cart/remove', 'App\Controllers\CartController@remove');
$router->get('/checkout', 'App\Controllers\CheckoutController@index');
$router->post('/checkout', 'App\Controllers\CheckoutController@place');
$router->get('/order/success/{number}', 'App\Controllers\CheckoutController@success');
$router->get('/account', 'App\Controllers\AccountController@index');
$router->post('/account/login', 'App\Controllers\AccountController@login');
$router->post('/account/register', 'App\Controllers\AccountController@register');
$router->get('/account/logout', 'App\Controllers\AccountController@logout');
$router->get('/account/orders', 'App\Controllers\AccountController@orders');
$router->get('/account/order/{id}', 'App\Controllers\AccountController@order');
$router->get('/page/{slug}', 'App\Controllers\PageController@show');
$router->get('/lang/{code}', 'App\Controllers\LangController@switch');

/* ---------- لوحة التحكم ---------- */
$router->get('/admin/login', 'App\Controllers\Admin\AuthController@loginForm');
$router->post('/admin/login', 'App\Controllers\Admin\AuthController@login');
$router->get('/admin/logout', 'App\Controllers\Admin\AuthController@logout');

$router->get('/admin', 'App\Controllers\Admin\DashboardController@index');

$router->get('/admin/products', 'App\Controllers\Admin\ProductController@index');
$router->get('/admin/products/new', 'App\Controllers\Admin\ProductController@create');
$router->get('/admin/products/{id}', 'App\Controllers\Admin\ProductController@edit');
$router->post('/admin/products/save', 'App\Controllers\Admin\ProductController@save');
$router->post('/admin/products/delete', 'App\Controllers\Admin\ProductController@delete');

$router->get('/admin/categories', 'App\Controllers\Admin\CategoryController@index');
$router->post('/admin/categories/save', 'App\Controllers\Admin\CategoryController@save');
$router->post('/admin/categories/delete', 'App\Controllers\Admin\CategoryController@delete');

$router->get('/admin/orders', 'App\Controllers\Admin\OrderController@index');
$router->get('/admin/orders/{id}/invoice', 'App\Controllers\Admin\OrderController@invoice');
$router->get('/admin/orders/{id}', 'App\Controllers\Admin\OrderController@show');
$router->post('/admin/orders/status', 'App\Controllers\Admin\OrderController@status');
$router->post('/admin/orders/delete', 'App\Controllers\Admin\OrderController@delete');

$router->get('/admin/customers', 'App\Controllers\Admin\CustomerController@index');
$router->get('/admin/customers/{id}', 'App\Controllers\Admin\CustomerController@show');

$router->get('/admin/wilayas', 'App\Controllers\Admin\WilayaController@index');
$router->post('/admin/wilayas/save', 'App\Controllers\Admin\WilayaController@save');

$router->get('/admin/users', 'App\Controllers\Admin\UserController@index');
$router->post('/admin/users/save', 'App\Controllers\Admin\UserController@save');
$router->post('/admin/users/delete', 'App\Controllers\Admin\UserController@delete');

$router->get('/admin/settings', 'App\Controllers\Admin\SettingsController@index');
$router->post('/admin/settings', 'App\Controllers\Admin\SettingsController@save');

$router->get('/admin/pages', 'App\Controllers\Admin\PageController@index');
$router->post('/admin/pages/save', 'App\Controllers\Admin\PageController@save');
$router->post('/admin/pages/delete', 'App\Controllers\Admin\PageController@delete');

$router->get('/admin/logs', 'App\Controllers\Admin\LogController@index');

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
