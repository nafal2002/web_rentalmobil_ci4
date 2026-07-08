<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Frontend Routes
$routes->get('/', 'Home::index');
$routes->get('product', 'Product::index');
$routes->get('product/detail/(:num)', 'Product::detail/$1');
$routes->get('product/checkout/(:num)', 'Product::checkout/$1');
$routes->post('product/save/(:num)/(:num)', 'Product::save/$1/$2');
$routes->get('account', 'Account::index');

// Admin Routes
$routes->get('admin', 'Layout::index', ['filter' => 'role:admin']);
$routes->get('dashboard/admin', 'Dashboard::admin', ['filter' => 'role:admin']);

$routes->group('admin', ['namespace' => 'App\\Controllers', 'filter' => 'role:admin'], function ($routes) {
	$routes->get('/', 'Layout::index');
	$routes->get('login', 'Login::index');
	$routes->post('login', 'Admin\\Login::login');
	$routes->get('logout', 'Admin\\Login::logout');
	$routes->get('checkout', 'Backend/checkout');
});

// Authentication Routes
$routes->group('auth', ['namespace' => 'IonAuth\Controllers'], function ($routes) {
	$routes->add('login', 'Auth::login');
	$routes->get('logout', 'Auth::logout');
	$routes->add('forgot_password', 'Auth::forgot_password');
});

// REST API Routes - Bookings
$routes->group('api/v1', ['namespace' => 'App\\Controllers\\Api'], function ($routes) {
	// Booking endpoints
	$routes->get('bookings', 'BookingController::index');
	$routes->post('bookings', 'BookingController::create');
	$routes->get('bookings/(:num)', 'BookingController::show/$1');
	$routes->put('bookings/(:num)', 'BookingController::update/$1');
	$routes->delete('bookings/(:num)', 'BookingController::delete/$1');
	$routes->get('bookings/customer/(:num)', 'BookingController::customerBookings/$1');

	// Payment endpoints
	$routes->get('payments', 'PaymentController::index');
	$routes->post('payments', 'PaymentController::create');
	$routes->get('payments/(:num)', 'PaymentController::show/$1');
	$routes->put('payments/(:num)/status', 'PaymentController::updateStatus/$1');
	$routes->get('payments/revenue', 'PaymentController::revenue');

	// Analytics endpoints
	$routes->get('analytics', 'AnalyticsController::index');
	$routes->get('analytics/latest', 'AnalyticsController::latest');
	$routes->post('analytics/calculate', 'AnalyticsController::calculate');

	// Reviews endpoints
	$routes->get('reviews/(:num)', 'ReviewController::index/$1');
	$routes->post('reviews', 'ReviewController::create');
	$routes->get('reviews/average', 'ReviewController::average');
});

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
