<?php

$this->respond("GET", "/terms-and-conditions/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./home/information-site.twig', [
        'title' => 'Términos y condiciones'
    ]);
});

$this->respond("GET", "/privacy-policies/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./home/information-site.twig', [
        'title' => 'Políticas de privacidad'
    ]);
});
