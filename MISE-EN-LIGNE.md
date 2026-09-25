# Y-stream : mise en ligne (Netlify + domaine Infomaniak)

> **Depuis le 25/09/2026, le site est hébergé chez Infomaniak** (hébergement « ease designers SITES », dossier `/sites/y-stream.fr`, publication par GitHub Actions, back-office par e-mail + mot de passe). La procédure Netlify ci-dessous est conservée pour l'historique ; la référence à jour est le guide « Y-stream – structure et maintenance.pdf » (généré par `python3 tools/make-guide.py`).


Contexte : le domaine y-stream.fr et la messagerie info@y-stream.fr sont chez Infomaniak, le site actuel est sur Wix.
On ne touche pas à la messagerie : seuls les enregistrements « web » (A et CNAME) changent, les MX restent.
Le site Wix reste en ligne jusqu'au basculement DNS, il n'y a donc aucune coupure.

Les étapes marquées **[Ivan]** demandent tes comptes ou tes identifiants, je ne peux pas les faire à ta place.
Les étapes marquées **[Claude]** sont faites ou seront faites par moi.

## 1. Dépôt Git **[Claude – fait]**

Le dossier `Y-stream` est un dépôt Git (premier commit fait). Ne sont pas versionnés : `dist/`, `apercu/`, les sources lourdes
(`.ai`, film original, PNG haute définition), voir `.gitignore`.

## 2. GitHub **[Ivan, 10 min]**

1. Créer un compte sur github.com (gratuit) si tu n'en as pas, avec ivan@ease-designers.com.
2. Installer GitHub Desktop (desktop.github.com), se connecter.
3. GitHub Desktop → File → Add Local Repository → choisir `Documents/Claude/Projects/Y-stream`.
4. Bouton « Publish repository » → nom `y-stream`, décocher « Keep this code private » ou le laisser coché (privé fonctionne aussi avec Netlify) → Publish.

À partir de là, chaque modification que je fais dans le dossier apparaît dans GitHub Desktop ; un clic sur « Commit » puis « Push » la met en ligne (Netlify reconstruit le site en ~30 s).

## 3. Netlify **[Ivan, 10 min]**

1. Créer un compte sur netlify.com en choisissant « Sign up with GitHub ».
2. « Add new site » → « Import an existing project » → GitHub → choisir `y-stream`.
3. Netlify lit `netlify.toml` : build `node build.js`, dossier `dist`. Laisser tel quel → Deploy.
4. Le site est visible sur une adresse provisoire du type `https://y-stream-xxxx.netlify.app`. C'est le moment de tout vérifier (mobile, formulaire, EN/FR, popup vidéo).
5. Site configuration → Forms → activer « Form detection » (le formulaire de contact sera reconnu au déploiement suivant). Forms → Notifications → ajouter une notification e-mail vers info@y-stream.fr.
6. Site configuration → Identity → Enable Identity → Registration : « Invite only » → Services → Git Gateway : Enable. Puis Identity → Invite users → ton adresse (et celle de Pierre s'il doit éditer). Chacun reçoit un e-mail pour choisir un mot de passe : c'est l'accès au back-office `https://www.y-stream.fr/admin/`.

## 4. Domaine **[Ivan, 10 min, puis 1 à 24 h de propagation]**

Dans Netlify : Domain management → Add a domain → `y-stream.fr` → « Add domain » (choisir de garder les DNS externes, ne pas
transférer les DNS chez Netlify, sinon il faudrait recréer les MX de la messagerie). Netlify affiche alors les valeurs à
configurer ; elles sont normalement celles-ci :

| Type | Nom | Valeur |
|---|---|---|
| A | @ (y-stream.fr) | 75.2.60.5 |
| CNAME | www | `<nom-du-site>.netlify.app` |

Dans Infomaniak : Manager → Domaines → y-stream.fr → Zone DNS :
1. Noter (capture d'écran) la zone complète avant modification.
2. Supprimer les enregistrements A de `@` qui pointent vers Wix (185.230.63.x) et le CNAME `www` vers `wixdns.net`.
3. Ajouter l'enregistrement A `@` → 75.2.60.5 et le CNAME `www` → `<nom-du-site>.netlify.app`.
4. Ne rien toucher aux MX, SPF (TXT), DKIM, DMARC : c'est la messagerie.

Après propagation, Netlify émet automatiquement le certificat HTTPS (Let's Encrypt). Dans Domain management, définir
`www.y-stream.fr` comme domaine principal (les canonicals du site pointent vers www).

## 5. Après la bascule **[Claude + Ivan]** — bascule effective le 18/09/2026, site en ligne sur www.y-stream.fr le 21/09/2026

- Vérifier https://www.y-stream.fr, https://y-stream.fr (redirection vers www), /fr/, /admin/, le formulaire (un vrai envoi).
- Google Search Console : ajouter la propriété `y-stream.fr` (validation par enregistrement TXT chez Infomaniak) et soumettre `https://www.y-stream.fr/sitemap.xml`. **[Ivan]** pour la création, je prépare les valeurs.
- Résilier l'abonnement Wix une fois le nouveau site stable (garder le compte quelques semaines pour récupérer d'éventuels contenus).
- Mentions légales complétées le 21/09/2026 (capital 15 000 €, directeur de la publication Ivan Bellia, hébergeur Netlify, Inc.).
- Formulaire testé, notification e-mail vers info@y-stream.fr active. Badge « Powered by Netlify » désactivé.
- Note : la délégation DNS (rubrique « Modifier les serveurs DNS » chez Infomaniak, distincte de la zone DNS) a mis plusieurs heures à être acceptée par le registre AFNIC ; vérifiable sur https://rdap.nic.fr/domain/y-stream.fr.

## Rappels

- Modifier un texte : soit via `/admin/` (Fiona-style, sans outil), soit en éditant `data/*.json` puis Commit + Push dans GitHub Desktop.
- Tester en local : `node build.js --preview` puis ouvrir `apercu/accueil.html`.
- Le film complet (`assets/video/y-stream-film.mp4`, 10 Mo) est dans le dépôt ; la version sans texte incrusté pourra le remplacer au même nom.
