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
        'anotherAction' => 'Don\'t have an account?',
        'path' => 'signin'
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
        'anotherAction' => 'Have an account?',
        'path' => 'register'
      )
    );
  });
  $router->respond('POST', '/register', function(
    $request, $response, $service, $app
  ) {
    session_start();
    $stmt = $app->db->prepare(
      'SELECT user_name FROM users
      WHERE user_name = :nm'
    );
    $stmt->bindValue(':nm', $_POST['name']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $reg = $app->db->prepare(
      'INSERT INTO users (user_name, user_password)
      VALUES (:nm, :pw)'
    );

    if (
      !empty($user) &&
      strlen($_POST['name']) >= 3
      && strlen($_POST['password']) >= 3
    ) {
      $response->redirect('./register', $code = 302);
    } else {
      $reg->bindValue(':nm', $_POST['name']);
      $reg->bindValue(':pw', md5($_POST['password']));
      $reg->execute();
      $lastId = $app->db->lastInsertId();

      $_SESSION['user_name'] = $_POST['name'];
      $_SESSION['user_id'] = $lastId;

      $response->redirect('./', $code = 302);
    }

  });
  $router->respond('POST', '/user', function(
    $request, $response, $service, $app
  ) {
    session_start();
    $stmt = $app->db->prepare(
      'SELECT user_name FROM users
      WHERE user_name = :nm'
    );
    $stmt->bindValue(':nm', $_POST['name']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user)) {
      echo "yes";
    } else {
      echo "no";
    }
  });
  $router->respond('POST', '/signin', function(
    $request, $response, $service, $app
  ) {
    session_start();
    $stmt = $app->db->prepare(
      'SELECT user_id, user_name, user_password
      FROM users WHERE user_name = :nm'
    );
    $stmt->bindValue(':nm', $_POST['name']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user)) {
      if ($user['user_password'] == md5($_POST['password'])) {
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_id'] = $user['user_id'];
        echo "ok";
      } else {
        echo "incorrectPassword";
      }
    } else {
      echo "incorrectName";
    }
  });


  try {
    $router->dispatch();
  }
  catch (Exception $e) {
    echo $e->getMessage();
  }
?>
