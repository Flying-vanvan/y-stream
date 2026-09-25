<?php
// Decap CMS – connexion GitHub (étape 2) : échange le code contre un jeton et le transmet au back-office.
require __DIR__ . '/config.php';
session_start();
header('Content-Type: text/html; charset=utf-8');

$code  = $_GET['code']  ?? '';
$state = $_GET['state'] ?? '';
$ok    = false;
$payload = '';

if ($code !== '' && $state !== '' && hash_equals($_SESSION['oauth_state'] ?? '', $state)) {
  unset($_SESSION['oauth_state']);
  $ch = curl_init('https://github.com/login/oauth/access_token');
  curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_POSTFIELDS => http_build_query([
      'client_id'     => OAUTH_CLIENT_ID,
      'client_secret' => OAUTH_CLIENT_SECRET,
      'code'          => $code,
      'redirect_uri'  => OAUTH_REDIRECT_URI,
      'state'         => $state,
    ]),
    CURLOPT_TIMEOUT => 15,
  ]);
  $res = curl_exec($ch);
  curl_close($ch);
  $data = json_decode((string)$res, true);
  if (!empty($data['access_token'])) {
    $ok = true;
    $payload = json_encode(['token' => $data['access_token'], 'provider' => 'github'], JSON_UNESCAPED_SLASHES);
  } else {
    $payload = json_encode(['error' => $data['error_description'] ?? 'Échec de la connexion GitHub']);
  }
} else {
  $payload = json_encode(['error' => 'Requête invalide (state)']);
}
$status = $ok ? 'success' : 'error';
?>
<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><title>Connexion GitHub</title></head>
<body>
<p>Connexion en cours…</p>
<script>
(function () {
  var status = <?= json_encode($status) ?>;
  var payload = <?= $payload ?>;
  var msg = 'authorization:github:' + status + ':' + JSON.stringify(payload);
  function receive(e) {
    window.opener.postMessage(msg, e.origin);
    window.removeEventListener('message', receive, false);
    setTimeout(function () { window.close(); }, 300);
  }
  window.addEventListener('message', receive, false);
  if (window.opener) window.opener.postMessage('authorizing:github', '*');
  else document.body.innerHTML = '<p>Fenêtre ouverte hors du back-office. Fermez-la et réessayez depuis /admin/.</p>';
})();
</script>
</body></html>
