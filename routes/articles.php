<?php
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
    $voutes_stmt = $app->db->prepare(
      'SELECT * FROM voutes
      WHERE publication_id = :p_id'
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

      $voutes_stmt->bindValue(':p_id', $publication_id);
      $voutes_stmt->execute();
      $likes = $voutes_stmt->fetchAll(PDO::FETCH_ASSOC);
      $vouted = false;

      if (isset($_SESSION['user_id'])) {
        foreach ($likes as $k => $v) {
          if ($v['user_id'] == $_SESSION['user_id']) {
            $vouted = true;
          }
        }
      }


      $result[$key] = array(
        'id' => $publication_id,
        'title' => $post['title'],
        'date' => $post['creation_date'],
        'user' => $user_name['user_name'],
        'text' => $app->parse->text($text['text']),
        'voutes' => count($likes),
        'vouted' => $vouted
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
?>
