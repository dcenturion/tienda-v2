<?php

$this->respond("GET", "/edit/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./user/user-edit.twig');
});

$this->respond("GET", "/logout/?", function ($request, $response, $service) {
    //  Realizando el proceso de cierre de sesión
    /* ... */
    ////////////////////////////////////////////////////////////////////////
    // Simulando un proceso de cierre de sesión
    unset($_SESSION["userSessionStarted"]);
    ////////////////////////////////////////////////////////////////////////
    
    // Redirecting
    $response->redirect('/');
});

$this->respond("GET", "/recover-account/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./user/user-recover-account.twig');
});

$this->respond("GET", "/recover-account/confirmed/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./user/user-recover-account-confirmed.twig');
});

$this->respond("GET", "/register/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./user/user-register.twig');
});

$this->respond("GET", "/register/confirmed/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./user/user-register-confirmed.twig');
});

$this->respond("GET", "/register/confirmed-account/?", function ($request, $response, $service) {
    // Render
    return $service->twig->render('./user/user-register-confirmed-account.twig');
});

$this->respond("POST", "/login/?", function ($request, $response, $service) {
    //  Realizando el proceso de inicio de sesión
    /* ... */
    ////////////////////////////////////////////////////////////////////////
    // Simulando un proceso de inicio de sesión
    $_SESSION["userSessionStarted"] = true;
    ////////////////////////////////////////////////////////////////////////
    
    // Redirecting
    $response->redirect('/');
});

$this->respond("POST", "/register/?", function ($request, $response, $service) {
    //  Realizando el proceso registro de un nuevo usuario
    /* ... */
    
    // Redirecting
    $response->redirect('/user/register/confirmed');
});

$this->respond("POST", "/recover-account/?", function ($request, $response, $service) {
    //  Realizando el proceso de recuperación de la cuenta del usuario
    /* ... */
    
    // Redirecting
    $response->redirect('/user/recover-account/confirmed');
});