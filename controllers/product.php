<?php

$this->respond("GET", "/[:productId]/?", function ($request, $response, $service) {
    $productId = $request->param('productId');

    // Obteniendo información del producto
    /* ... */
    ////////////////////////////////////////////////////////////////////////
    // Información de demostración
    $products = [
        'curso-virtual-de-especializacion-en-gestion-de-procesos'   => [
            'productNamePrefix' => 'Curso virtual de especialización en',
            'productName'       => 'Gestión de Procesos',
            'type'              => 'virtual-course',
        ],
        'curso-presencial-de-especializacion-en-recursos-humanos'   => [
            'productNamePrefix' => 'Curso presencial de especialización en',
            'productName'       => 'Recursos Humanos',
            'type'              => 'face-to-face-course',
        ],
        'laptop-hp-fg382-92'                                        => [
            'productNamePrefix' => 'Laptop HP FG382-92',
            'productName'       => 'Laptop HP FG382-92',
            'type'              => 'physical',
        ],
        'ingresos-del-gobierno-central'                             => [
            'productNamePrefix' => 'Ingresos del Gobierno Central',
            'productName'       => 'Ingresos del Gobierno Central',
            'type'              => 'virtual',
        ],
    ];
    ////////////////////////////////////////////////////////////////////////
    
    // Render
    return $service->twig->render('./products/product-view.twig', [
        "product" => $products[$productId]
    ]);
});
