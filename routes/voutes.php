<?php
  $router->respond('GET', '/voutes/[:id]', function(
    $request, $response, $service, $app
  ) {
    $voutes = $app->db->prepare(
      'SELECT * FROM voutes
      WHERE publication_id = :p_id'
    );
    $voutes->bindValue(':p_id', $request->id);
    $voutes->execute();
    $likes = $voutes->fetchAll(PDO::FETCH_ASSOC);
    echo count($likes);
  });



  $router->respond('POST', '/voutes/[:id]', function(
    $request, $response, $service, $app
  ) {
    $get_voute = $app->db->prepare(
      'SELECT * FROM voutes
      WHERE (publication_id = :pid)
      AND (user_id = :uid)'
    );
    $insert_voute = $app->db->prepare(
      'INSERT INTO voutes
      (publication_id, user_id)
      VALUES (:pid, :uid)'
    );
    $delete_voute = $app->db->prepare(
      'DELETE FROM voutes
      WHERE (publication_id = :pid)
      AND (user_id = :uid)'
    );
    session_start();
    if (isset($_SESSION['user_id'])) {

      $user_id = $_SESSION['user_id'];
      $publication_id = $request->id;

      $get_voute->bindValue(
        ':pid', $publication_id
      );
      $get_voute->bindValue(
        ':uid', $user_id
      );
      $get_voute->execute();
      $voute = $get_voute->fetchAll(
        PDO::FETCH_ASSOC
      );

      if (count($voute) == 0) {
        $insert_voute->bindValue(
          ':pid', $publication_id
        );
        $insert_voute->bindValue(
          ':uid', $user_id
        );

        $insert_voute->execute();
        echo 'vouted';
      } else {
        $delete_voute->bindValue(
          ':pid', $publication_id
        );
        $delete_voute->bindValue(
          ':uid', $user_id
        );

        $delete_voute->execute();
        echo 'unvouted';
      }

    } else {
      echo 'error';
    }
  });
?>
