<?php
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
?>
