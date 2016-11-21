<?php
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
?>
