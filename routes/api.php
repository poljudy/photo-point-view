<?php
$router->group(['prefix' => 'api'], function () use ($router) {

    /**
     * Authentication related routes
     */
    $router->group(['prefix' => 'auth', 'namespace' => 'Auth'], function () use ($router) {

        $router->post('/login', "AuthController@login");
        $router->post('/register', "AuthController@register");

        $router->post('/forgot-password', "AuthController@resetPasswordFn");
        $router->post('/change-password', "AuthController@changePasswordFn");
        /** FIXME  Remain To Set Change Password From App */
    });

    /**
     * after login routes access
     */
    $router->group(['middleware' => ["auth:api"]], function () use ($router) {

        $router->get('get-all-data', "CommonController@getAllCommonData");

        $router->group(['prefix' => 'account'], function () use ($router) {
            $router->post('get-address-list', 'User\UserController@getAddressList');
            $router->post('add-address', 'User\UserController@storeAddress');
            $router->put('update-address/{id}', 'User\UserController@updateAddress');
            $router->get('get-address/{id}', 'User\UserController@showAddress');
            $router->delete('delete-address/{id}', 'User\UserController@destroy');
            $router->get('set-active-address/{id}', 'User\UserController@setToActiveAddress');
        });

        /** get all products list */
        $router->post('product-list', "Product\ProductController@list");
        $router->post('add-to-cart', "Cart\CartController@store");
        $router->post('cart-list', "Cart\CartController@list");
        $router->post('delete-to-cart/{id}', "Cart\CartController@destroye");
        $router->post('remove-cart-quantity/{id}', "Cart\CartController@substractCartQuantity");
        // $router->group(['prefix' => 'products'])

        // $router->post('update-latitude-longitude', "AllInOneController@updateLatLongAPI");

    });
});
