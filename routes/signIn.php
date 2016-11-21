<?php
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
?>
