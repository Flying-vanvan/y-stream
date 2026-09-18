# Y-stream — inventaire du site actuel et brief de refonte

Dossier de travail : `Documents/Claude/Projects/Y-stream`
Site actuel : https://www.y-stream.fr (Wix, en français uniquement)
Objectif : site vitrine bilingue EN/FR, minimaliste, esprit start-up innovante, destiné aux investisseurs et partenaires potentiels. Contenu à étoffer, design à moderniser.

## 1. Inventaire du site Wix (relevé le 18/09/2026)

### Pages
- **Accueil** — one-page, seule page publique.
- **Technologie** et **À propos** — réservées aux membres (connexion Wix). Contenu non accessible sans compte → à récupérer auprès d'Ivan.
- **Politique de confidentialité**.

### Identité visuelle actuelle
- Logo : « Y » avec triangle jaune-vert (#d7da35) + « stream » en gris anthracite (#3c3c3b). Récupéré en SVG : `assets/logo-y-stream.svg`.
- Couleurs : noir / blanc, gris #6e6e6e, bleu lien #116dff (bleu Wix par défaut, pas une couleur de marque).
- Typos Wix : Space Grotesk (titres de section en capitales), Helvetica Light (grands titres 58 px), Helvetica (texte 15 px), Poppins Semibold.
- Sections alternées fond noir / fond blanc, grands titres fins, boutons « LIRE PLUS » (qui pointent vers les pages membres).

### Contenus (textes intégraux de l'accueil)
- Hero : vidéo de fond 1280×720 (id Wix cfdcde_6972…).
- **VISION** — « Les cubes sont pratiques, mais ne sont pas si aérodynamiques... » / « Utiliser notre expérience aéronautique au service du transport routier pour développer une solution d'optimisation aérodynamique... »
- **NOTRE PRODUIT** — « Un appendice aérodynamique unique... »
- **LE CONSTAT** — « L'arrière c'est important » / 30 à 35 % de la traînée totale / Fort impact énergétique / « Une forme arrière abrupte (comme un hayon vertical) crée une grande zone de séparation de l'écoulement (zone de turbulences et de basse pression), augmentant la traînée. »
- **L'IDÉE** — « Un appendice textile » / Profiler l'arrière / Réduire la consommation / « Nous avons imaginé un appendice textile qui se gonfle avec le vent relatif. Le gonflage est mécanique et ajustable en fonction de la vitesse. Aucune augmentation du maître-couple afin de satisfaire aux normes en vigueur. Une réduction de la traînée liée à une économie d'énergie. »
- **POURQUOI C'EST BIEN ?** — Gain énergétique 10 % / Un produit simple / Un marché à fort impact / « ... »
- **CONCRÈTEMENT** — Poche textile / Châssis en aluminium / Dimensions normées / « Notre solution aérodynamique combine une haute technicité en matière de performance et de conception, avec une fabrication optimisée et une mise en œuvre simple. Une innovation de pointe, pensée pour être efficace, fiable et facilement déployable à grande échelle. »
- **POURQUOI Y-Stream ?** — « Une approche différente, un système simple qui marche... » / « ... »
- Seconde vidéo 1920×1080 (id Wix cfdcde_f713…).
- **L'ENTREPRISE – Où nous en sommes** — 1 brevet / 14 prototypes réalisés / 5 campagnes d'essai en soufflerie, un design validé / 1 cadre réglementaire européen validé.
- **Nos partenaires – COLLABORATION** — 3 logos partenaires (562×241 px, noms à confirmer).
- Footer : « Vous êtes intéressés ? » / Politique de confidentialité / LinkedIn (profil Ivan Bellia) / info@y-stream.fr / Tél : 06 08 28 69 39 (le lien `tel:` est un faux numéro 123-456-7890, à corriger) / 72 avenue Isola Bella, 06400 Cannes / © 2025 ease lab.

### Images sur Wix (à fournir en original si possible)
| Fichier Wix | Description | Taille |
|---|---|---|
| cube roues.png | illustration « cube sur roues » (camion = cube) | 1383×1440 |
| YS_aero.png | schéma aérodynamique | 827×827 |
| Capture d'écran 2025-10-27 | capture (simulation / CAO ?) | 2831×1265 |
| IMG_5688 copie_edited.jpg | photo prototype | 1326×1326 |
| YS_work.png | illustration produit | 1068×1068 |
| YS_vue arrière.jpg | vue arrière camion équipé | 4000×2000 |
| 3 logos partenaires | png sur fond transparent | 562×241 |

Points faibles relevés : pages clés cachées derrière une connexion, plusieurs sections « ... » non rédigées, aucun anglais, aucun appel à l'action pour un investisseur, pas de balises SEO (description, OG image), numéro de téléphone erroné dans le lien, marque « ease lab » vs « Y-stream » à clarifier.

## 2. Proposition de structure (à valider)

Site one-page + pages secondaires, EN à la racine / FR sous `/fr/`, même moteur de build que le site Axcience (statique, sans framework, back-office Decap pour éditer textes, chiffres, actualités et documents).

1. **Hero** — vidéo courte en fond (extrait muet, 10–15 s, poster image), accroche en une phrase : *Making trucks slip through the air* / *Rendre les camions aérodynamiques*, sous-titre (gain 10 % de carburant, brevet, essais en soufflerie validés), deux boutons : « Investor deck » et « Contact ».
2. **Le problème** — 30–35 % de la traînée vient de l'arrière ; 3 chiffres clés (traînée, part du carburant, CO₂ ou coût annuel par camion — à sourcer).
3. **La solution** — l'appendice textile gonflé par le vent relatif, 3 points (profilage, gonflage mécanique ajustable, aucune augmentation du maître-couple), schéma YS_aero, vue arrière.
4. **Comment ça marche** — 3 étapes (poche textile / châssis aluminium / dimensions normées), animation ou séquence de la vidéo.
5. **Pourquoi Y-stream** — simplicité, coût, déploiement à grande échelle, conformité réglementaire européenne.
6. **Traction / où nous en sommes** — compteurs animés : 1 brevet, 14 prototypes, 5 campagnes soufflerie, cadre réglementaire validé ; frise chronologique (création → brevet → essais → prochaines étapes).
7. **Marché** — taille du parc européen de poids lourds, réglementation (CO₂, VECTO…), cibles (constructeurs, carrossiers, flottes) — à documenter.
8. **Équipe & partenaires** — fondateur(s), conseillers, logos partenaires.
9. **Investisseurs & partenaires** — ce que Y-stream recherche (levée, partenaires industriels, pilotes flottes), bouton « Recevoir le dossier » (formulaire), téléchargement du deck sur demande.
10. **Actualités** (optionnel, alimenté via le back-office) — salons, essais, presse.
11. **Contact** + footer (mentions légales, confidentialité, LinkedIn).

Pages secondaires : mentions légales, politique de confidentialité, 404 ; éventuellement une page « Technologie » détaillée reprenant le contenu aujourd'hui réservé aux membres.

## 3. Direction artistique proposée

- Minimaliste, beaucoup de blanc, typographie grande et fine, une seule couleur d'accent : le jaune-vert du logo (#d7da35) sur fond anthracite (#3c3c3b) / blanc.
- Grille aérée, sections courtes, chiffres en grand, schémas épurés (lignes de flux d'air en accent).
- Animations discrètes : apparition au scroll, compteurs, lignes de flux animées sur le hero.
- Typo : une grotesque moderne auto-hébergée (ex. Space Grotesk pour les titres pour rester proche de l'existant, Inter pour le texte) — à confirmer.

## 4. Éléments à fournir par Ivan

- La vidéo de présentation (fichier source) + scénario actuel, pour en tirer l'extrait du hero et revoir le scénario.
- Contenu des pages membres « Technologie » et « À propos ».
- Images originales listées ci-dessus (ou accès pour les télécharger en bonne qualité).
- Noms des partenaires et logos vectoriels.
- Chiffres pour investisseurs : marché, gain mesuré en soufflerie/route, coût cible, calendrier, montant recherché (si communicable).
- Équipe : noms, rôles, photos.
- Entité juridique pour les mentions légales (Y-stream ? ease lab ? SIREN).
- Nom de domaine : garder y-stream.fr ; un .com pour l'international ?
