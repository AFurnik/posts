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

  // API
  $router->respond('GET', '/', function(
    $request, $response, $service, $app
  ) {
    $response->redirect('./articles', $code = 302);
  });

  $router->respond('GET', '/articles', function(
    $request, $response, $service, $app
  ) {
    session_start();

    $posts_stmt = $app->db->prepare(
      'SELECT * FROM publications'
    );
    $atricles_stmt = $app->db->prepare(
      'SELECT text FROM articles
      WHERE publication_id = :p_id'
    );
    $users_stmt = $app->db->prepare(
      'SELECT user_name FROM users
      WHERE user_id = :u_id'
    );
    $posts_stmt->execute();
    $posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = array();

    foreach ($posts as $key => $post) {
      $user_id = $post['author_id'];
      $publication_id = $post['publication_id'];

      $users_stmt->bindValue(':u_id', $user_id);
      $users_stmt->execute();
      $user_name = $users_stmt->fetch(PDO::FETCH_ASSOC);

      $atricles_stmt->bindValue(':p_id', $publication_id);
      $atricles_stmt->execute();
      $text = $atricles_stmt->fetch(PDO::FETCH_ASSOC);


      $result[$key] = array(
        'title' => $post['title'],
        'date' => $post['creation_date'],
        'user' => $user_name['user_name'],
        'text' => $app->parse->text($text['text'])
      );
    }

    if (isset($_SESSION['user_id'])) {
      echo $app->twig->render(
        'layout.twig',
        array(
          'title' => 'Dashboard',
          'user' => $_SESSION['user_name'],
          'posts' => $result
        )
      );
    } else {
      echo $app->twig->render(
        'layout.twig',
        array(
          'title' => 'Dashboard',
          'posts' => $result
        )
      );
    }

  });

  $router->respond('GET', '/articles/[:id]', function(
    $request, $response, $service, $app
  ) {
    session_start();

    $posts_stmt = $app->db->prepare(
      'SELECT * FROM publications
      WHERE publication_id = :p_id'
    );
    $atricles_stmt = $app->db->prepare(
      'SELECT text FROM articles
      WHERE publication_id = :p_id'
    );
    $users_stmt = $app->db->prepare(
      'SELECT user_name FROM users
      WHERE user_id = :u_id'
    );

    $publication_id = $request->id;
    $posts_stmt->bindValue(':p_id', $publication_id);
    $posts_stmt->execute();
    $post = $posts_stmt->fetch(PDO::FETCH_ASSOC);
    $result = array();

    if (!empty($post)) {
      $user_id = $post['author_id'];
      $users_stmt->bindValue(':u_id', $user_id);
      $users_stmt->execute();
      $user_name = $users_stmt->fetch(PDO::FETCH_ASSOC);

      $atricles_stmt->bindValue(':p_id', $publication_id);
      $atricles_stmt->execute();
      $text = $atricles_stmt->fetch(PDO::FETCH_ASSOC);

      $result[0] = array(
        'title' => $post['title'],
        'date' => $post['creation_date'],
        'user' => $user_name['user_name'],
        'text' => $app->parse->text($text['text'])
      );

      if (isset($_SESSION['user_id'])) {
        echo $app->twig->render(
          'layout.twig',
          array(
            'title' => 'Dashboard',
            'user' => $_SESSION['user_name'],
            'posts' => $result
          )
        );
      } else {
        echo $app->twig->render(
          'layout.twig',
          array(
            'title' => 'Dashboard',
            'posts' => $result
          )
        );
      }
    } else {
      echo $app->twig->render(
        'error.twig',
        array(
          'title' => 'Error',
          'errorHeader' => '404',
          'errorDesc' => 'Page Not Found'
        )
      );
    }



  });


  $router->respond('GET', '/my', function(
    $request, $response, $service, $app
  ) {
    session_start();

    $posts_stmt = $app->db->prepare(
      'SELECT * FROM publications
      WHERE author_id = :a_id'
    );
    $atricles_stmt = $app->db->prepare(
      'SELECT text FROM articles
      WHERE publication_id = :p_id'
    );
    $posts_stmt->bindValue(':a_id', $_SESSION['user_id']);
    $posts_stmt->execute();
    $posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = array();

    foreach ($posts as $key => $post) {
      $publication_id = $post['publication_id'];
      $user_name = $_SESSION['user_name'];

      $atricles_stmt->bindValue(':p_id', $publication_id);
      $atricles_stmt->execute();
      $text = $atricles_stmt->fetch(PDO::FETCH_ASSOC);

      $result[$key] = array(
        'title' => $post['title'],
        'date' => $post['creation_date'],
        'user' => $user_name,
        'text' => $app->parse->text($text['text'])
      );
    }

    if (isset($_SESSION['user_id'])) {
      echo $app->twig->render(
        'layout.twig',
        array(
          'title' => 'Dashboard',
          'user' => $_SESSION['user_name'],
          'posts' => $result
        )
      );
    } else {
      echo $app->twig->render(
        'layout.twig',
        array(
          'title' => 'Dashboard',
          'posts' => $result
        )
      );
    }

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

  $router->respond('GET', '/logout', function(
    $request, $response, $service, $app
  ) {
    session_start();
    session_destroy();
    $response->redirect('./', $code = 302);
  });

  $router->respond('GET', '/addpost', function(
    $request, $response, $service, $app
  ) {
    session_start();
    if (isset($_SESSION['user_id'])) {
      echo $app->twig->render(
        'addPostForm.twig',
        array(
          'title' => 'New post',
          'user' => $_SESSION['user_name']
        )
      );
    } else {
      $response->redirect('./', $code = 302);
    }
  });

  $router->respond('POST', '/addpost', function(
    $request, $response, $service, $app
  ) {
    session_start();
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $text = $_POST['text'];
    $date = date('Y:m:d H:i:s');

    $publication = $app->db->prepare(
      'INSERT INTO publications
      (title, author_id, creation_date)
      VALUES (:t, :a_id, :c_d)'
    );
    $article = $app->db->prepare(
      'INSERT INTO articles
      (publication_id, text)
      VALUES (:p_id, :t)'
    );

    if (
      isset($user_id) &&
      strlen($title) != 0 &&
      strlen($text) != 0
    ) {
      $publication->bindValue(':t', $title);
      $publication->bindValue(':a_id', $user_id);
      $publication->bindValue(':c_d', $date);
      $publication->execute();
      $publicationId = $app->db->lastInsertId();

      $article->bindValue(':p_id', $publicationId);
      $article->bindValue(':t', $text);
      $article->execute();

      $response->redirect('./', $code = 302);
    }
  });

  try {
    $router->dispatch();
  }
  catch (Exception $e) {
    echo $e->getMessage();
  }
?>
