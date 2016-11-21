<?php
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
?>
