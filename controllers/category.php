<?php
$this->respond("GET", "/[:categoryId]/?", function ($request, $response, $service) {
    $categoryId = $request->param('categoryId');
    
    // Content params
    $presentationNames = [
        'books'         => 'Libros',
        'courses'       => 'Cursos',
        'technology'    => 'Tecnología',
        'software'      => 'Software',
    ];
    
    // Render
    return $service->twig->render('./products/product-list-per-category.twig', [
        'categoryName' => $presentationNames[$categoryId]
    ]);
});