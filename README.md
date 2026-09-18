# Y-stream – site vitrine (refonte hors Wix)

Site statique bilingue (EN à la racine, FR sous `/fr/`), sans framework ni dépendance, généré par `build.js` (Node ≥ 18).
Public : investisseurs et partenaires potentiels (industriels, flottes, presse).

## Dossier

- `index.html` – gabarit de la page d'accueil (one-page) ; `page.html` – gabarit des pages légales ; `404.html` – page introuvable bilingue.
- `data/home.json` + `home.fr.json` – tous les textes de l'accueil ; `seo.json` – balises ; `ui.json` – menus, formulaire, popups ; `legal.json` – mentions légales, confidentialité, cookies.
- `assets/site.css` – design (blanc / anthracite #1f1f1e / jaune-vert #d7da35, polices Space Grotesk + Inter auto-hébergées dans `assets/fonts/`).
- `assets/img/` – images optimisées ; `assets/video/hero-loop.mp4` (boucle muette 11 s, 0,5 Mo) et `y-stream-film.mp4` (film complet 720p, 10 Mo) ; sources originales conservées dans `assets/` (PNG, `.ai`, `Mon film 2c.mp4`).
- `admin/` – back-office Decap CMS (tous les textes, chiffres, équipe, partenaires et pages légales sont éditables).
- `apercu/` – aperçus autonomes à ouvrir dans un navigateur : `accueil.html`, `fr-accueil.html`, `legal.html`, `privacy.html`, `cookies.html` (+ `fr-…`), `404.html`.

## Générer

```
node build.js            # → dist/
node build.js --preview  # → dist/ + apercu/
```

## Mise en ligne (Netlify ou Cloudflare Pages, gratuit)

1. Dépôt Git avec ce dossier ; build command `node build.js`, output `dist`.
2. Domaine y-stream.fr : changer les DNS chez le registrar une fois le site validé (garder Wix en ligne jusque-là).
3. Formulaire : Netlify Forms (`action="/"` + `data-netlify="true"`, champ anti-spam caché) ; sur Cloudflare Pages remplacer `action` par une URL Formspree et retirer `data-netlify`. Envoi en arrière-plan et popup « Votre message a bien été envoyé / Your message has been sent ».
4. Back-office : `admin/config.yml` → renseigner `repo:` ; avec Netlify, `backend: git-gateway` + Netlify Identity pour se connecter par e-mail.
5. Anciennes URL Wix redirigées dans `_redirects` (`/technologie`, `/à-propos`, `/politique-de-confidentialité`).
6. Sécurité : `_headers` (HSTS, CSP stricte sans domaine externe, X-Frame-Options…). Aucun cookie de suivi, aucun appel externe.

## SEO

Titres/descriptions par langue, canonical, hreflang en/fr/x-default, Open Graph + Twitter (`assets/og-y-stream.jpg` 1200×630), Schema.org (Organization ease lab SAS avec SIREN/TVA, WebSite, Product, WebPage), sitemap avec alternates, robots.txt, page 404 noindex.

## À compléter avant mise en ligne

- Mentions légales : capital social, directeur de la publication, hébergeur (`data/legal*.json`, marqués `[à compléter]`).
- Photos des fondateurs (`team.members[].photo`, ex. `/assets/img/pierre-herpin.jpg`) – en attendant, initiales.
- Chiffres à valider : « 90 % des marchandises transportées par la route » (interprétation du pictogramme du dossier de presse), 2 500 h/an, 56 000 L/an.
- Traduction anglaise à relire par un locuteur natif ; scénario du film à revoir (le film actuel contient des textes incrustés en français à partir de 47 s, d'où la boucle muette coupée avant).
- Choix de l'hébergeur (Europe / USA) et création des comptes GitHub + Netlify/Cloudflare.
