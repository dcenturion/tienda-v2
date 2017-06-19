<?php

$this->respond("GET", "/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./orders/order-list.twig');
});