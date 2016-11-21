<?php
  $router->respond('GET', '/', function(
    $request, $response, $service, $app
  ) {
    $response->redirect('./articles', $code = 302);
  });
?>
