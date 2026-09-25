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

if (!function_exists('mail')) $fail(500, 'Fonction mail() désactivée : à activer dans le Manager Infomaniak (site → Avancé → PHP / Apache)');
if (!@mail(TO, $mailSubject, $body, $headers)) $fail(500, 'Envoi impossible');

// Sans JavaScript : retour à la page avec un marqueur
if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') === false) {
  header('Location: ' . ($lang === 'fr' ? '/fr/' : '/') . '?sent=1#contact', true, 303);
  exit;
}
echo json_encode(['ok' => true]);
