<?php
// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates');

    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$container['renderer'] = function () {
    $renderer = new \Slim\Views\PhpRenderer("templates");
    return $renderer;
};

//Guzzle HTTP client
$container['httpClient'] = function() {
    $guzzle = new GuzzleHttp\Client();
    return $guzzle;
};

