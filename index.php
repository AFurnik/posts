<?php
  require_once 'routes/config.php';

  // API
  require_once 'routes/index.php';
  require_once 'routes/articles.php';
  require_once 'routes/articleById.php';
  require_once 'routes/myArticles.php';
  require_once 'routes/signIn.php';
  require_once 'routes/register.php';
  require_once 'routes/logout.php';
  require_once 'routes/addpost.php';

  try {
    $router->dispatch();
  }
  catch (Exception $e) {
    echo $e->getMessage();
  }
?>
