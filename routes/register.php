<?php
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

?>
