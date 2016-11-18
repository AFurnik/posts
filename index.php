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
  });

  // API
  $router->respond('GET', '/', function(
    $request, $response, $service, $app
  ) {
    echo $app->twig->render(
      'layout.twig',
      array(
        'title' => 'Dashboard'
      )
    );
  });

  $router->respond('GET', '/signin', function(
    $request, $response, $service, $app
  ) {
    echo $app->twig->render(
      'sign.twig',
      array(
        'title' => 'Sign in. Posts.',
        'header' => 'Sign into your account',
        'action' => './signin',
        'button' => 'Sign in',
        'anotherActionLink' => './register',
        'anotherAction' => 'Don\'t have an account?'
      )
    );
  });

  $router->respond('GET', '/register', function(
    $request, $response, $service, $app
  ) {
    echo $app->twig->render(
      'sign.twig',
      array(
        'title' => 'Register. Posts.',
        'header' => 'Create new account',
        'action' => './register',
        'button' => 'Create Account',
        'anotherActionLink' => './signin',
        'anotherAction' => 'Have an account?'
      )
    );
  });
  $router->respond('POST', '/register', function(
    $request, $response, $service, $app
  ) {
    
  });


  try {
    $router->dispatch();
  }
  catch (Exception $e) {
    echo $e->getMessage();
  }
?>
