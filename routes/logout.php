<?php
  $router->respond('GET', '/logout', function(
    $request, $response, $service, $app
  ) {
    session_start();
    session_destroy();
    $response->redirect('./', $code = 302);
  });
?>
