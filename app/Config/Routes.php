<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index', ['filter' => 'auth']);

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->group('produk', ['filter' => 'auth'], function ($routes) { 
    $routes->get('', 'ProdukController::index');
    $routes->post('', 'ProdukController::create');
    $routes->post('edit/(:any)', 'ProdukController::edit/$1');
    $routes->get('delete/(:any)', 'ProdukController::delete/$1');
    $routes->get('download', 'ProdukController::download');
});

$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TransaksiController::index');
    $routes->post('', 'TransaksiController::cart_add');
    $routes->post('edit', 'TransaksiController::cart_edit');
    $routes->get('delete/(:any)', 'TransaksiController::cart_delete/$1');
    $routes->get('clear', 'TransaksiController::cart_clear');
});

$routes->get('checkout', 'TransaksiController::checkout', ['filter' => 'auth']);
$routes->post('buy', 'TransaksiController::buy', ['filter' => 'auth']);
$routes->get('history', 'TransaksiController::history', ['filter' => 'auth']);
$routes->get('checkout', 'TransaksiController::checkout', ['filter' => 'auth']);
$routes->post('buy', 'TransaksiController::buy', ['filter' => 'auth']);
$routes->get('history', 'TransaksiController::history', ['filter' => 'auth']);
$routes->get('profile', 'AuthController::profile', ['filter' => 'auth']);

$routes->get('ajax/destinations','TransaksiController::destinations', ['filter' => 'auth']);
$routes->get('ajax/costs','TransaksiController::costs', ['filter' => 'auth']);

$routes->group('api', function ($routes) {

    // transaction.rest -> GET /api/transactions
    $routes->get('transactions', 'TransaksiController::apiIndex');

    // product.rest -> ProdukController
    $routes->get('products', 'ProdukController::index');
    $routes->get('products/(:num)', 'ProdukController::show/$1');
    $routes->post('products', 'ProdukController::create');
    $routes->put('products/(:num)', 'ProdukController::update/$1');
    $routes->patch('products/(:num)', 'ProdukController::update/$1');
    $routes->delete('products/(:num)', 'ProdukController::delete/$1');

});
