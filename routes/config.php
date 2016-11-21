<?php
  require_once 'vendor/autoload.php';

  // Klein configuration
  $base = dirname($_SERVER['PHP_SELF']);
  if (ltrim($base, '/')) {
    $_SERVER['REQUEST_URI'] =
    substr(
      $_SERVER['REQUEST_URI'], strlen($base)
    );
  }
  $router = new \Klein\Klein();

  // Twig configuration
  $router->respond(function(
    $request, $response, $service, $app
  ) use ($klein) {
    $app->register('twig', function() {
      $loader = new Twig_Loader_Filesystem(
        'templates'
      );
      return $twig = new Twig_Environment(
        $loader,
        array('auto_reload' => true)
      );
    });
    $app->register('db', function() {
        return new PDO(
          "mysql:host=localhost;port=3306;dbname=posts",
          "root",
          "root"
        );
    });
    $app->register('parse', function() {
      return new Parsedown();
    });
  });
?>
