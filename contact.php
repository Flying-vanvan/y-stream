<?php
// Formulaire de contact Y-stream → e-mail à info@y-stream.fr (hébergement Infomaniak, fonction mail()).
// Appelé en AJAX par la page (réponse JSON), fonctionne aussi sans JavaScript (redirection).
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

const TO      = 'info@y-stream.fr';
const FROM    = 'info@y-stream.fr';   // doit être l'« adresse e-mail d'expédition » définie dans le Manager Infomaniak (site → Avancé → Général)
const MAX_LEN = 5000;

$fail = function (int $code, string $msg) {
  http_response_code($code);
  echo json_encode(['ok' => false, 'error' => $msg]);
  exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') $fail(405, 'Méthode non autorisée');
// Anti-spam : champ caché rempli uniquement par les robots, et envoi trop rapide impossible à détecter sans JS → on garde simple.
if (!empty($_POST['website'])) { echo json_encode(['ok' => true]); exit; }   // on fait croire au robot que c'est passé

$clean = fn($k) => trim(mb_substr((string)($_POST[$k] ?? ''), 0, MAX_LEN));
$name = $clean('name'); $email = $clean('email'); $company = $clean('company');
$subject = $clean('subject'); $message = $clean('message'); $lang = $clean('lang') === 'fr' ? 'fr' : 'en';

if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $fail(422, 'Champs manquants ou e-mail invalide');
if (preg_match('/[\r\n]/', $name . $email . $subject)) $fail(422, 'Valeur invalide');   // injection d'en-têtes

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$body = "Nouveau message depuis www.y-stream.fr ($lang)\n\n"
      . "Nom : $name\nE-mail : $email\nSociété : $company\nSujet : $subject\n\n"
      . "Message :\n$message\n\n— envoyé le " . date('d/m/Y H:i') . " (IP $ip)";

$mailSubject = '=?UTF-8?B?' . base64_encode("[Y-stream] $subject – $name") . '?=';
$headers = "From: Y-stream <" . FROM . ">\r\n"
         . "Reply-To: " . str_replace(['<', '>'], '', $name) . " <$email>\r\n"
         . "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n";

// Envoi : SMTP authentifié Infomaniak si le fichier privé existe (recommandé), sinon fonction mail().
// Fichier privé (hors du site, créé via WebFTP) : /sites/private/y-stream-mail.php contenant
//   <?php define('SMTP_USER', 'info@y-stream.fr'); define('SMTP_PASS', 'mot de passe de la boîte');
$private = dirname($_SERVER['DOCUMENT_ROOT']) . '/private/y-stream-mail.php';
if (is_file($private)) {
  require $private;
  $err = smtp_send('mail.infomaniak.com', 465, SMTP_USER, SMTP_PASS, FROM, [TO], "To: " . TO . "\r\nSubject: $mailSubject\r\n" . $headers . "\r\n" . $body);
  if ($err !== null) $fail(500, 'Envoi impossible (' . $err . ')');
} else {
  if (!function_exists('mail')) $fail(500, 'Fonction mail() désactivée : à activer dans le Manager Infomaniak (site → Avancé → PHP / Apache)');
  if (!@mail(TO, $mailSubject, $body, $headers)) $fail(500, 'Envoi impossible');
}

// Sans JavaScript : retour à la page avec un marqueur
if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') === false) {
  header('Location: ' . ($lang === 'fr' ? '/fr/' : '/') . '?sent=1#contact', true, 303);
  exit;
}
echo json_encode(['ok' => true]);

// Mini client SMTP (SSL implicite, AUTH LOGIN). Retourne null si OK, sinon le message d'erreur.
function smtp_send(string $host, int $port, string $user, string $pass, string $from, array $to, string $data): ?string {
  $fp = @stream_socket_client("ssl://$host:$port", $errno, $errstr, 15);
  if (!$fp) return "connexion : $errstr";
  stream_set_timeout($fp, 15);
  $read = function () use ($fp) { $out = ''; while (($l = fgets($fp, 515)) !== false) { $out .= $l; if (!isset($l[3]) || $l[3] !== '-') break; } return $out; };
  $cmd = function (string $c, string $expect) use ($fp, $read) { fwrite($fp, $c . "\r\n"); $r = $read(); return strpos($r, $expect) === 0 ? null : trim($r); };
  if (strpos($read(), '220') !== 0) return 'pas de bannière SMTP';
  foreach ([["EHLO y-stream.fr", '250'], ["AUTH LOGIN", '334'], [base64_encode($user), '334'], [base64_encode($pass), '235'], ["MAIL FROM:<$from>", '250']] as [$c, $e]) if (($r = $cmd($c, $e)) !== null) return $r;
  foreach ($to as $rcpt) if (($r = $cmd("RCPT TO:<$rcpt>", '250')) !== null) return $r;
  if (($r = $cmd('DATA', '354')) !== null) return $r;
  $data = preg_replace('/^\./m', '..', str_replace(["\r\n", "\n"], ["\n", "\r\n"], $data));
  if (($r = $cmd($data . "\r\n.", '250')) !== null) return $r;
  $cmd('QUIT', '221'); fclose($fp);
  return null;
}
