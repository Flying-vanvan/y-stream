<?php
// Back-office Decap CMS : écran de connexion.
//  - e-mail + mot de passe (éditeurs) : le serveur vérifie le mot de passe puis transmet au back-office
//    le jeton GitHub du site (jamais visible dans le dépôt) ;
//  - ou « Connexion avec GitHub » (agence), si l'OAuth App est configurée.
require __DIR__ . '/config.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');

// Connexion GitHub (étape 1) : redirection vers l'écran d'autorisation GitHub
if (isset($_GET['github']) && GITHUB_ENABLED) {
  $state = bin2hex(random_bytes(16));
  $_SESSION['oauth_state'] = $state;
  header('Location: https://github.com/login/oauth/authorize?' . http_build_query([
    'client_id' => OAUTH_CLIENT_ID, 'redirect_uri' => OAUTH_REDIRECT_URI, 'scope' => 'repo,user', 'state' => $state,
  ]), true, 302);
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && LOGIN_ENABLED) {
  $_SESSION['tries'] = ($_SESSION['tries'] ?? 0) + 1;
  $email = strtolower(trim((string)($_POST['email'] ?? '')));
  $pass  = (string)($_POST['password'] ?? '');
  $csrf  = (string)($_POST['csrf'] ?? '');
  $ok = false;
  if ($_SESSION['tries'] > 8) {
    $error = 'Trop de tentatives. Réessayez dans quelques minutes.';
  } elseif (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
    $error = 'Session expirée, merci de réessayer.';
  } else {
    foreach ($BO_USERS as $u => $stored) {
      if (strtolower($u) !== $email) continue;
      $p = explode('$', (string)$stored);   // pbkdf2$itérations$sel$empreinte
      if (count($p) === 4 && $p[0] === 'pbkdf2') {
        $calc = bin2hex(hash_pbkdf2('sha256', $pass, hex2bin($p[2]), (int)$p[1], 32, true));
        $ok = hash_equals($p[3], $calc);
      }
    }
    if (!$ok) { usleep(800000); $error = 'E-mail ou mot de passe incorrect.'; }
  }
  if ($ok) {
    $_SESSION['tries'] = 0;
    $payload = json_encode(['token' => BO_TOKEN, 'provider' => 'github'], JSON_UNESCAPED_SLASHES);
    ?><!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><title>Connexion…</title></head><body>
<p style="font:16px Arial,sans-serif;text-align:center;margin-top:40px">Connexion en cours…</p>
<script>
(function () {
  var msg = 'authorization:github:success:' + JSON.stringify(<?= $payload ?>);
  function receive(e) { window.opener.postMessage(msg, e.origin); window.removeEventListener('message', receive, false); setTimeout(function () { window.close(); }, 300); }
  window.addEventListener('message', receive, false);
  if (window.opener) window.opener.postMessage('authorizing:github', '*');
  else document.body.innerHTML = '<p>Fenêtre ouverte hors du back-office. Fermez-la et reconnectez-vous depuis /admin/.</p>';
})();
</script></body></html><?php
    exit;
  }
}
$_SESSION['csrf'] = $_SESSION['csrf'] ?? bin2hex(random_bytes(16));
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Connexion au back-office | Y-stream</title>
<style>
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;display:grid;place-items:center;background:#1f1f1e;font:16px/1.5 Inter,"Helvetica Neue",Arial,sans-serif;color:#3c3c3b}
  .box{width:min(380px,92vw);background:#f6f6f2;border-radius:10px;padding:30px 28px 26px;box-shadow:0 20px 50px rgba(0,0,0,.35)}
  .logo{display:block;width:150px;margin:0 auto 14px}
  h1{font-size:20px;text-align:center;margin:0 0 20px;color:#1f1f1e}
  label{display:block;font-size:14px;margin:0 0 4px}
  input{width:100%;height:42px;border:1px solid rgba(31,31,30,.25);border-radius:6px;padding:0 12px;font:inherit;margin-bottom:14px;background:#fff}
  input:focus{outline:2px solid #d7da35;border-color:#a8ab1c}
  button{width:100%;height:44px;border:0;border-radius:22px;background:#d7da35;color:#1f1f1e;font:inherit;font-weight:700;cursor:pointer;transition:background .3s}
  button:hover{background:#c5c82a}
  .err{background:#fdecee;color:#b3102a;border-radius:6px;padding:8px 12px;font-size:14px;margin:0 0 14px}
  .alt{text-align:center;margin:18px 0 0;font-size:13px}
  .alt a{color:#3c3c3b}
  .help{text-align:center;font-size:13px;margin:12px 0 0;color:#7b7b78}
</style>
</head>
<body>
<main class="box">
  <img class="logo" src="/assets/logo-y-stream.svg" alt="Y-stream">
  <h1>Back-office du site</h1>
  <?php if (LOGIN_ENABLED): ?>
  <?php if ($error): ?><p class="err" role="alert"><?= $h($error) ?></p><?php endif; ?>
  <form method="post" autocomplete="on">
    <input type="hidden" name="csrf" value="<?= $h($_SESSION['csrf']) ?>">
    <label for="email">E-mail</label>
    <input id="email" name="email" type="email" required autocomplete="username" value="<?= $h($_POST['email'] ?? '') ?>" autofocus>
    <label for="password">Mot de passe</label>
    <input id="password" name="password" type="password" required autocomplete="current-password">
    <button type="submit">Se connecter</button>
  </form>
  <p class="help">Mot de passe oublié : contactez ease designers.</p>
  <?php else: ?>
  <p class="err">La connexion par e-mail n'est pas encore configurée.</p>
  <?php endif; ?>
  <?php if (GITHUB_ENABLED): ?><p class="alt"><a href="?github=1">Connexion avec un compte GitHub (agence)</a></p><?php endif; ?>
</main>
</body>
</html>
