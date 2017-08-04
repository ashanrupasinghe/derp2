<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    $routes->connect('/products/category/:slug', ['controller' => 'Products', 'action' => 'category'], array('slug' => 'slug'));
    $routes->connect('/product/:slug', ['controller' => 'Products', 'action' => 'view'], array('pass' => array('slug')));
	
    $routes->connect('/category/:slug', array('controller' => 'Category', 'action' => 'index'), array('pass' => array('slug')));
    $routes->connect('/cart', ['controller' => 'Cart', 'action' => 'getCart']);

    $routes->connect('/user/login', ['controller' => 'Users', 'action' => 'login']);
    $routes->connect('/user/register', ['controller' => 'Users', 'action' => 'register']);
    $routes->connect('/user/logout', ['controller' => 'Users', 'action' => 'logout']);

    $routes->connect('/user/changepassword', ['controller' => 'Users', 'action' => 'forgotpassword']);
    $routes->connect('/user/forgotpassword', ['controller' => 'Users', 'action' => 'requestPassword']);
    $routes->connect('/user/resetpasswordtoken/*', ['controller' => 'Users', 'action' => 'resetpasswordtoken']);
    $routes->connect('/user/resetpasswordtoken', ['controller' => 'Users', 'action' => 'resetpasswordtoken']);
    $routes->connect('/user/verifytoken', ['controller' => 'Users', 'action' => 'verifytoken']);

    $routes->connect('/user/dashboard', ['controller' => 'Cart', 'action' => 'dashboard']);
    $routes->connect('/user/cart', ['controller' => 'Cart', 'action' => 'getcart']);
    $routes->connect('/user/wishlist', ['controller' => 'Cart', 'action' => 'getwishlist']);
    $routes->connect('/order/checkout', ['controller' => 'Cart', 'action' => 'checkout']);
    $routes->connect('/cart/deleteproduct', ['controller' => 'Cart', 'action' => 'deleteproduct']);
    $routes->connect('/user/deletewishlistitem', ['controller' => 'Cart', 'action' => 'deleteWishListItem']);
    $routes->connect('/user/addwishlistitem', ['controller' => 'Cart', 'action' => 'addWishListItem']);
    $routes->connect('/cart/quickedit', ['controller' => 'Cart', 'action' => 'quickedit']);
    $routes->connect('/order/view/:id', ['controller' => 'Orders', 'action' => 'viewOrder'], ['pass' => ['id']]);
    $routes->connect('/order/reorder', ['controller' => 'Cart', 'action' => 'placeOrder']);
    $routes->connect('/user/iswishlistitem', ['controller' => 'Cart', 'action' => 'isWishListItem']);
	
	$routes->connect('/:slug', ['controller' => 'Products', 'action' => 'view'], array('pass' => array('slug')));
    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks(DashedRoute::class);
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
