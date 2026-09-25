<?php
// Charge les identifiants de l'OAuth App GitHub depuis un fichier PRIVÉ situé HORS du dossier web,
// pour qu'ils ne soient jamais dans le dépôt ni téléchargeables.
// Sur l'hébergement Infomaniak : créer le fichier  /sites/private/y-stream-oauth.php  (via WebFTP) contenant :
//   <?php
//   define('OAUTH_CLIENT_ID', 'Iv1.xxxxxxxx');
//   define('OAUTH_CLIENT_SECRET', 'xxxxxxxxxxxxxxxx');
$private = dirname($_SERVER['DOCUMENT_ROOT']) . '/private/y-stream-oauth.php';
if (!is_file($private)) { http_response_code(500); exit('Configuration OAuth manquante (fichier privé absent).'); }
require $private;
define('OAUTH_REDIRECT_URI', 'https://www.y-stream.fr/admin/oauth/callback.php');
