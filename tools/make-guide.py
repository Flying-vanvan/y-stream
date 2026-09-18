#!/usr/bin/env python3
"""Génère 'Y-stream – structure et maintenance.pdf' (reportlab)."""
from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm
from reportlab.lib import colors
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.enums import TA_LEFT
from reportlab.platypus import (SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether)

INK = colors.HexColor('#1f1f1e'); GREY = colors.HexColor('#3c3c3b'); MUTED = colors.HexColor('#7b7b78')
LIME = colors.HexColor('#d7da35'); LIMED = colors.HexColor('#a8ab1c'); SOFT = colors.HexColor('#f6f6f2'); LINE = colors.HexColor('#e6e6e1')

OUT = 'Y-stream – structure et maintenance.pdf'
doc = SimpleDocTemplate(OUT, pagesize=A4, leftMargin=20*mm, rightMargin=20*mm, topMargin=22*mm, bottomMargin=20*mm,
                        title='Y-stream – structure et maintenance du site', author='ease designers', subject='y-stream.fr')

H1 = ParagraphStyle('h1', fontName='Helvetica-Bold', fontSize=22, leading=26, textColor=INK, spaceAfter=4)
H2 = ParagraphStyle('h2', fontName='Helvetica-Bold', fontSize=14, leading=18, textColor=INK, spaceBefore=14, spaceAfter=6)
EYE = ParagraphStyle('eye', fontName='Helvetica-Bold', fontSize=8.5, leading=11, textColor=LIMED, spaceBefore=2, spaceAfter=2)
P = ParagraphStyle('p', fontName='Helvetica', fontSize=10, leading=14.5, textColor=GREY, spaceAfter=6)
SM = ParagraphStyle('sm', parent=P, fontSize=9, leading=12.5, textColor=MUTED)
CODE = ParagraphStyle('code', fontName='Courier', fontSize=9, leading=12.5, textColor=INK, backColor=SOFT, borderPadding=(4, 6, 4, 6), spaceAfter=8, spaceBefore=2)
TH = ParagraphStyle('th', fontName='Helvetica-Bold', fontSize=9, leading=12, textColor=INK)
TD = ParagraphStyle('td', fontName='Helvetica', fontSize=9, leading=12.5, textColor=GREY)
TDC = ParagraphStyle('tdc', fontName='Courier', fontSize=8.6, leading=12.5, textColor=INK)

def p(t, s=P): return Paragraph(t, s)
def code(t): return Paragraph(t.replace(' ', '&nbsp;').replace('\n', '<br/>'), CODE)
def table(rows, widths, mono_first=False):
    data = [[Paragraph(c, TH) for c in rows[0]]] + [[Paragraph(c, TDC if (mono_first and i == 0) else TD) for i, c in enumerate(r)] for r in rows[1:]]
    t = Table(data, colWidths=widths, repeatRows=1)
    t.setStyle(TableStyle([('LINEBELOW', (0, 0), (-1, 0), 1.2, LIME), ('LINEBELOW', (0, 1), (-1, -1), 0.4, LINE),
                           ('VALIGN', (0, 0), (-1, -1), 'TOP'), ('TOPPADDING', (0, 0), (-1, -1), 5), ('BOTTOMPADDING', (0, 0), (-1, -1), 5),
                           ('LEFTPADDING', (0, 0), (-1, -1), 4), ('RIGHTPADDING', (0, 0), (-1, -1), 4)]))
    t.spaceAfter = 8
    return t

def header_footer(canvas, doc_):
    canvas.saveState()
    w, h = A4
    canvas.setFillColor(LIME); canvas.rect(20*mm, h - 14*mm, 9*mm, 1.6*mm, stroke=0, fill=1)
    canvas.setFont('Helvetica-Bold', 8); canvas.setFillColor(INK)
    canvas.drawString(31*mm, h - 13.6*mm, 'Y-STREAM  ·  STRUCTURE ET MAINTENANCE DU SITE')
    canvas.setFont('Helvetica', 8); canvas.setFillColor(MUTED)
    canvas.drawRightString(w - 20*mm, h - 13.6*mm, 'ease designers · septembre 2026')
    canvas.drawRightString(w - 20*mm, 12*mm, f'{doc_.page}')
    canvas.drawString(20*mm, 12*mm, 'y-stream.fr  ·  dépôt github.com/Flying-vanvan/y-stream  ·  hébergement Netlify')
    canvas.restoreState()

S = []
# ---------------- Page 1 : vue d'ensemble
S += [p('Y-stream', H1), p('Structure du site et guide de maintenance', ParagraphStyle('sub', parent=P, fontSize=12, leading=16, textColor=MUTED, spaceAfter=14))]
S += [p('EN UNE PHRASE', EYE), p("Le site y-stream.fr est un site statique bilingue (anglais à la racine, français sous <b>/fr/</b>) généré par un petit script "
       "à partir de fichiers texte, versionné sur GitHub, publié automatiquement par Netlify et modifiable par un back-office web. "
       "Aucune base de données, aucun serveur à entretenir, aucun abonnement payant.")]
S += [p('COMMENT LES PIÈCES S’EMBOÎTENT', EYE)]
S += [table([['Brique', 'Rôle', 'Où'],
             ['Dossier Y-stream', 'Les sources du site (textes, images, gabarits, script de génération). Copie locale du dépôt.', 'Mac : Documents/Claude/Projects/Y-stream'],
             ['GitHub', 'La référence : chaque modification (depuis le Mac ou le back-office) y est enregistrée avec son historique.', 'github.com/Flying-vanvan/y-stream'],
             ['Netlify', 'À chaque modification sur GitHub, exécute <font face="Courier">node build.js</font>, publie le dossier <font face="Courier">dist/</font>, gère HTTPS, le formulaire et les comptes du back-office.', 'app.netlify.com → projet ystream'],
             ['Back-office', 'Interface web pour modifier textes, chiffres, équipe, images et pages légales sans outil. Écrit directement dans GitHub.', 'www.y-stream.fr/admin/'],
             ['Infomaniak', 'Nom de domaine et messagerie info@y-stream.fr. Ne change pas.', 'manager.infomaniak.com']],
            [32*mm, 88*mm, 50*mm])]
S += [p('LE CYCLE DE PUBLICATION', EYE),
      p('1. Une modification est enregistrée dans GitHub (par le back-office, ou par GitHub Desktop depuis le Mac).<br/>'
        '2. Netlify la détecte, régénère le site en ~30 secondes et le publie.<br/>'
        '3. Le site est à jour. Un « Fetch origin » dans GitHub Desktop rapatrie les changements du back-office sur le Mac.')]
S += [p('Les deux sens fonctionnent : ce que tu changes dans le back-office se retrouve dans le dossier du Mac, et inversement. '
        'La seule règle : toujours faire « Fetch origin » avant de modifier des fichiers sur le Mac, pour ne pas écraser une modification faite dans le back-office.', SM)]

# ---------------- Structure du dossier
S += [PageBreak(), p('Structure du dossier', H2)]
S += [table([['Fichier / dossier', 'Contenu', 'À modifier ?'],
             ['data/home.json', 'Tous les textes de la page d’accueil en anglais (bandeau, chiffres, constat, idée, étapes, avantages, développement, suite, équipe, contact).', 'Oui, via le back-office ou un éditeur de texte'],
             ['data/home.fr.json', 'Idem en français.', 'Oui'],
             ['data/seo.json / seo.fr.json', 'Titre, description, mots-clés, image de partage.', 'Oui'],
             ['data/ui.json / ui.fr.json', 'Menus, libellés du formulaire, popups, bandeau cookies, page 404.', 'Rarement'],
             ['data/legal.json / legal.fr.json', 'Mentions légales, confidentialité, cookies (format Markdown).', 'Oui'],
             ['assets/img/', 'Images optimisées du site = la médiathèque du back-office.', 'Ajouter / remplacer'],
             ['assets/video/', 'hero-loop.mp4 (boucle muette du bandeau) et y-stream-film.mp4 (film complet).', 'Remplacer au même nom'],
             ['assets/fonts/', 'Polices Space Grotesk et Inter auto-hébergées.', 'Non'],
             ['assets/site.css', 'Tout le design (couleurs, typos, animations, responsive).', 'Avec Claude'],
             ['index.html, page.html, 404.html', 'Gabarits des pages : accueil, pages légales, page introuvable.', 'Avec Claude'],
             ['partials-footer.html, partials-cookie.js', 'Pied de page et bandeau cookies, communs à toutes les pages.', 'Avec Claude'],
             ['build.js', 'Le générateur : injecte les données dans les gabarits, produit EN + FR, le sitemap, les redirections, les en-têtes de sécurité.', 'Avec Claude'],
             ['admin/', 'Back-office Decap CMS (config.yml décrit les champs éditables).', 'Avec Claude'],
             ['netlify.toml, robots.txt, .gitignore', 'Configuration de l’hébergement, des robots, des fichiers exclus du dépôt.', 'Non'],
             ['apercu/', 'Aperçus autonomes générés par <font face="Courier">node build.js --preview</font> ; hors dépôt.', 'Généré'],
             ['dist/', 'Site généré ; hors dépôt, reconstruit par Netlify.', 'Généré'],
             ['assets/*.ai, Mon film 2c.mp4, 260731_*.png', 'Sources lourdes (Illustrator, film original, PNG HD). Sur le Mac uniquement, exclues du dépôt.', 'Archive']],
            [46*mm, 90*mm, 34*mm], mono_first=True)]
S += [p('Le dossier <font face="Courier">_to_delete/</font> contient des fichiers temporaires de Git à supprimer ; il est ignoré par le dépôt.', SM)]

# ---------------- Modifier le contenu
S += [PageBreak(), p('Modifier le contenu', H2)]
S += [p('PAR LE BACK-OFFICE (RECOMMANDÉ)', EYE),
      p('Aller sur <b>www.y-stream.fr/admin/</b>, se connecter avec l’e-mail et le mot de passe Netlify Identity. '
        'Deux collections : <b>Site – English</b> et <b>Site – Français</b>, chacune avec la page d’accueil, le SEO, les textes d’interface et les pages légales. '
        'Modifier, puis <b>Publier</b> : le site est régénéré en une minute. Pour une image, l’onglet <b>Media</b> permet de téléverser dans assets/img puis de la sélectionner dans le champ concerné (photo d’un fondateur, logo partenaire).')]
S += [p('Chaque publication du back-office est un commit GitHub signé de ton nom : l’historique complet est consultable et tout retour arrière est possible.', SM)]
S += [p('DEPUIS LE MAC', EYE),
      p('1. GitHub Desktop → <b>Fetch origin</b> (récupère les éventuelles modifications du back-office).<br/>'
        '2. Modifier les fichiers dans le dossier Y-stream (ou demander à Claude).<br/>'
        '3. Vérifier en local : <font face="Courier">node build.js --preview</font> puis ouvrir <font face="Courier">apercu/accueil.html</font> ou <font face="Courier">apercu/fr-accueil.html</font>.<br/>'
        '4. GitHub Desktop → écrire un résumé → <b>Commit to main</b> → <b>Push origin</b>. Netlify publie.')]
S += [p('RÈGLES DE RÉDACTION INTÉGRÉES', EYE),
      p('Le générateur applique automatiquement la typographie : espace insécable avant : ; ? ! % et », les deux-points ne passent jamais seuls à la ligne. '
        'Un retour à la ligne dans un titre s’écrit <font face="Courier">\\n</font> dans le JSON (utilisé dans « Physics pays the bill » et « puis aller sur la route »). '
        'Les tirets longs sont évités dans les textes (virgules). En français, le dispositif s’appelle la <b>coiffe</b> ; en anglais, the <b>tail</b>.')]
S += [p('IMAGES ET VIDÉOS', EYE),
      p('Images : JPEG qualité 85 pour les photos et rendus, PNG pour les logos et la pastille (fond transparent), largeur maximale 1800 px. '
        'Photos des fondateurs : carré 600 × 600. Pastilles : 300 × 300, une par langue (badge-fr.png / badge-en.png). '
        'Vidéo du bandeau : MP4 H.264 1280 × 720 sans son, 8 à 12 s, ~0,5 Mo, sans texte incrusté (bilingue). Film complet : 1280 × 720, ~10 Mo. '
        'Pour remplacer, déposer le nouveau fichier au même nom dans assets/video/ et publier.')]

# ---------------- Hébergement, comptes, domaine
S += [p('Hébergement, comptes et domaine', H2)]
S += [table([['Service', 'Compte', 'Sert à'],
             ['GitHub', 'Flying-vanvan (compte personnel existant)', 'Héberger le dépôt y-stream. Donne accès à Netlify en lecture/écriture sur ce seul dépôt.'],
             ['Netlify', 'ivan@ease-designers.com (connexion via GitHub)', 'Build, hébergement, HTTPS, formulaire (Forms), comptes du back-office (Identity + Git Gateway), domaine.'],
             ['Netlify Identity', 'Utilisateurs invités : Ivan (+ Pierre si besoin)', 'Connexion au back-office. Inscription en « Invite only ». Mot de passe oublié : lien « Forgot password » sur /admin/.'],
             ['Infomaniak', 'Compte existant', 'Domaine y-stream.fr (zone DNS) et messagerie info@y-stream.fr. Seuls les enregistrements A de @ et CNAME de www pointent vers Netlify ; MX, SPF, DKIM restent Infomaniak.'],
             ['Wix', 'Ancien site', 'À résilier après quelques semaines de stabilité du nouveau site.']],
            [30*mm, 55*mm, 85*mm])]
S += [p('DNS (chez Infomaniak, zone y-stream.fr)', EYE),
      table([['Type', 'Nom', 'Valeur'], ['A', '@', '75.2.60.5 (Netlify)'], ['CNAME', 'www', 'ystream.netlify.app'], ['MX / TXT', '@', 'inchangés (messagerie Infomaniak)']], [25*mm, 25*mm, 120*mm])]
S += [p('Domaine principal dans Netlify : <b>www.y-stream.fr</b> (les canonicals du site pointent vers www ; y-stream.fr redirige vers www). Certificat HTTPS Let’s Encrypt automatique.', SM)]

# ---------------- Sécurité, SEO, formulaire
S += [PageBreak(), p('Sécurité, SEO et formulaire', H2)]
S += [p('SÉCURITÉ', EYE),
      p('Site statique : pas de base de données ni de code serveur, donc pas de faille applicative à surveiller. En-têtes envoyés par Netlify (fichier <font face="Courier">_headers</font> généré) : '
        'Content-Security-Policy stricte (aucun domaine externe sauf pour /admin/), HSTS, X-Frame-Options DENY, nosniff, Referrer-Policy. Aucun cookie de suivi, aucun script tiers sur les pages publiques. '
        'Le back-office est protégé par Netlify Identity ; l’accès en écriture au dépôt passe par Git Gateway, sans jamais exposer de jeton GitHub.')]
S += [p('SEO', EYE),
      p('Par page et par langue : titre, description, canonical, hreflang en / fr / x-default, Open Graph et Twitter (image assets/og-y-stream.jpg 1200 × 630), données structurées Schema.org '
        '(Organization ease lab SAS avec SIREN et TVA, WebSite, Product, WebPage), sitemap.xml avec alternates, robots.txt, page 404 en noindex. '
        'Redirections des anciennes URL Wix : /technologie, /à-propos, /politique-de-confidentialité. '
        'À faire après la bascule : déclarer la propriété dans Google Search Console (validation par enregistrement TXT chez Infomaniak) et soumettre https://www.y-stream.fr/sitemap.xml.')]
S += [p('FORMULAIRE DE CONTACT', EYE),
      p('Netlify Forms : les messages arrivent dans Netlify → Forms → contact et par e-mail si une notification est configurée (Forms → Notifications → e-mail vers info@y-stream.fr). '
        'Envoi en arrière-plan, popup « Votre message a bien été envoyé » ; champ anti-spam caché (honeypot). Limite du plan gratuit : 100 messages par mois.')]

# ---------------- Que faire si
S += [p('Que faire si…', H2)]
S += [table([['Situation', 'Réponse'],
             ['Le site ne se met pas à jour après une publication', 'Netlify → Deploys : vérifier que le dernier déploiement est « Published ». S’il est en erreur, ouvrir le journal ; le plus souvent un JSON invalide (guillemet ou virgule manquante) dans data/. Corriger et republier.'],
             ['Le back-office refuse la connexion', 'Netlify → Identity → Users : le compte existe-t-il ? Sinon, « Invite users ». Mot de passe oublié : lien sur la page /admin/. Vérifier que Git Gateway est activé (Identity → Services).'],
             ['Un lien d’invitation ouvre la page d’accueil', 'Le site redirige automatiquement vers /admin/ ; sinon ajouter « admin/ » avant le « # » dans l’adresse.'],
             ['Les mails info@y-stream.fr n’arrivent plus', 'Sans rapport avec le site : vérifier la zone DNS Infomaniak (MX, SPF) qui ne doit pas avoir été modifiée lors de la bascule.'],
             ['Revenir à une version précédente', 'Netlify → Deploys → choisir un déploiement antérieur → « Publish deploy » (instantané). Ou GitHub Desktop → History → Revert.'],
             ['Ajouter un éditeur (Pierre)', 'Netlify → Identity → Invite users avec son e-mail. Il reçoit un lien, choisit un mot de passe.'],
             ['Remplacer le film ou la boucle', 'Encoder en MP4 H.264 720p, déposer au même nom dans assets/video/, Commit + Push.'],
             ['Changer une couleur, une police, une animation', 'assets/site.css ; demander à Claude en indiquant l’élément et l’effet souhaité.'],
             ['Ajouter une page ou une section', 'Nouveau bloc dans index.html + textes dans data/home*.json + champs dans admin/config.yml ; avec Claude.'],
             ['Quitter Netlify un jour', 'Le dossier + <font face="Courier">node build.js</font> suffisent : dist/ se dépose sur n’importe quel hébergeur statique (Cloudflare Pages, OVH, Infomaniak). Seuls le formulaire et la connexion au back-office seraient à reconfigurer.']],
            [50*mm, 120*mm])]

# ---------------- Reste à faire
S += [p('Reste à faire au lancement', H2),
      p('• Bascule DNS chez Infomaniak (voir tableau) puis domaine principal www.y-stream.fr dans Netlify.<br/>'
        '• Mentions légales : capital social et directeur de la publication (marqués [à compléter] dans data/legal*.json) ; hébergeur : Netlify, Inc., 512 2nd Street, San Francisco, CA 94107, USA.<br/>'
        '• Netlify → Forms : activer la détection, ajouter la notification e-mail, faire un envoi de test.<br/>'
        '• Retirer le badge « Powered by Netlify » (Project configuration → General).<br/>'
        '• Google Search Console et sitemap.<br/>'
        '• Contenus à valider : chiffre « 90 % des marchandises par la route », formulation « directive européenne à laquelle nous participons », relecture de l’anglais, coquille « COMSUMPTION » sur la pastille anglaise, version du film sans textes incrustés.<br/>'
        '• Résiliation Wix après quelques semaines.')]

doc.build(S, onFirstPage=header_footer, onLaterPages=header_footer)
print('ok', OUT)
