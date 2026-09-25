<?php
// Réglages PRIVÉS du back-office, lus depuis un fichier situé HORS du dossier web (jamais dans le dépôt, jamais téléchargeable).
// Sur l'hébergement Infomaniak : /sites/private/y-stream-oauth.php (créé via WebFTP), voir MISE-EN-LIGNE.md § 3 :
//   <?php
//   // 1) Connexion simple par e-mail + mot de passe (éditeurs)
//   define('BO_TOKEN', 'github_pat_…');            // jeton GitHub « fine-grained » limité au dépôt y-stream (Contents : Read and write)
//   $BO_USERS = [                                   // une ligne par personne, générée avec : node tools/mot-de-passe.js
//     'aurelie@exemple.fr' => 'pbkdf2$200000$…$…',
//   ];
//   // 2) Facultatif : connexion avec un compte GitHub (agence)
//   define('OAUTH_CLIENT_ID', '…');
//   define('OAUTH_CLIENT_SECRET', '…');
// Deux sources possibles (la première trouvée) :
//  - secrets.php, écrit à chaque déploiement par GitHub Actions à partir des secrets BO_TOKEN / BO_PASSWORD du dépôt ;
//  - le fichier privé /sites/private/y-stream-oauth.php (plusieurs éditeurs, connexion GitHub de l'agence).
$BO_USERS = [];
$private = dirname($_SERVER['DOCUMENT_ROOT']) . '/private/y-stream-oauth.php';
if (is_file($private)) require $private;
elseif (is_file(__DIR__ . '/secrets.php')) require __DIR__ . '/secrets.php';
else { http_response_code(500); exit('Configuration du back-office manquante (secrets BO_TOKEN / BO_PASSWORD du dépôt GitHub).'); }
define('OAUTH_REDIRECT_URI', 'https://www.y-stream.fr/admin/oauth/callback.php');
define('GITHUB_ENABLED', defined('OAUTH_CLIENT_ID') && defined('OAUTH_CLIENT_SECRET'));
define('LOGIN_ENABLED', defined('BO_TOKEN') && !empty($BO_USERS));
