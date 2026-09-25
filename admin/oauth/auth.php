<?php
// Decap CMS – connexion GitHub (étape 1) : redirige vers l'écran d'autorisation GitHub.
// Les identifiants de l'OAuth App sont lus depuis un fichier PRIVÉ hors du site (voir config.php).
require __DIR__ . '/config.php';
session_start();
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;
$params = http_build_query([
  'client_id'    => OAUTH_CLIENT_ID,
  'redirect_uri' => OAUTH_REDIRECT_URI,
  'scope'        => 'repo,user',
  'state'        => $state,
]);
header('Location: https://github.com/login/oauth/authorize?' . $params, true, 302);
exit;
