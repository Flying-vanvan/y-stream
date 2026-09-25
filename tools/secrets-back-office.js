#!/usr/bin/env node
// Utilisé par GitHub Actions (deploy.yml) : écrit dist/admin/oauth/secrets.php à partir des secrets du dépôt
// BO_TOKEN et BO_PASSWORD. Le mot de passe n'est jamais envoyé en clair : seule son empreinte PBKDF2 est écrite.
// Le fichier est interdit en téléchargement (admin/oauth/.htaccess) et, étant du PHP, n'affiche rien s'il est appelé.
const fs = require('fs'), path = require('path'), crypto = require('crypto');
const token = (process.env.BO_TOKEN || '').trim(), pass = process.env.BO_PASSWORD || '';
const emails = (process.env.BO_EMAIL || '').split(',').map(e => e.trim().toLowerCase()).filter(Boolean);
if (!token || !pass) { console.log('Back-office : secrets BO_TOKEN / BO_PASSWORD absents, étape ignorée.'); process.exit(0); }
if (!/^[A-Za-z0-9_]+$/.test(token)) { console.log('::error::BO_TOKEN invalide (copier le jeton github_pat_… en entier, sans espace).'); process.exit(1); }
if (pass.length < 10) { console.log('::error::BO_PASSWORD trop court (10 caractères minimum).'); process.exit(1); }
const it = 200000, lines = emails.map(e => {
  const salt = crypto.randomBytes(16), hash = crypto.pbkdf2Sync(pass, salt, it, 32, 'sha256').toString('hex');
  return `  '${e.replace(/'/g, '')}' => 'pbkdf2$${it}$${salt.toString('hex')}$${hash}',`;
});
const out = path.join(__dirname, '..', 'dist', 'admin', 'oauth', 'secrets.php');
fs.mkdirSync(path.dirname(out), { recursive: true });
fs.writeFileSync(out, `<?php\n// Généré à chaque déploiement par GitHub Actions — ne pas modifier ici (Settings → Secrets du dépôt).\ndefine('BO_TOKEN', '${token}');\n$BO_USERS = [\n${lines.join('\n')}\n];\n`);
console.log(`Back-office : accès configuré pour ${emails.join(', ')}.`);
