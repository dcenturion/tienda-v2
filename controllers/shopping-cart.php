<?php

$this->respond("GET", "/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./shopping-cart/shopping-cart-product-list.twig');
});

$this->respond("GET", "/checkout/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./shopping-cart/shopping-cart-checkout.twig');
});

$this->respond("POST", "/checkout/?", function ($request, $response, $service) {
    //  Realizando el proceso de compra
    /* ... */
    
    // Redirecting
    $response->redirect('/shopping-cart/checkout-confirmed');
});

$this->respond("GET", "/checkout-confirmed/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./shopping-cart/shopping-cart-checkout-confirmed.twig');
});