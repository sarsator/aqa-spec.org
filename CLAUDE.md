# CLAUDE.md — Projet aqa-spec.org

## Identite du projet
- **Nom** : AQA Specification Website
- **Domaine** : aqa-spec.org (heberge chez OVH, 100 Mo, hebergement statique)
- **But** : Site officiel du standard AQA (AI Question Answer), un standard ouvert pour enrichir les FAQ avec des metadonnees optimisees pour les IA conversationnelles
- **Proprietaire** : Davy Abderrahman, fondateur d'AI Labs Solutions (SASU, Saint-Benigne, Ain, France)
- **Licence** : MIT
- **Repo GitHub de la spec** : https://github.com/sarsator/aqa-specification
- **Version actuelle de la spec** : v1.2.2-draft

## Architecture
- Site 100% statique (HTML/CSS/JS vanilla, AUCUN framework, AUCUN backend, AUCUNE base de donnees)
- Hebergement OVH mutualise avec FTP
- Aucune dependance npm, aucun build step, aucun bundler
- Le site doit peser moins de 5 Mo total
- HTTPS obligatoire (gere par OVH)

## Structure des fichiers
```
aqa-spec.org/
├── index.html
├── style.css
├── ns/
│   └── context.jsonld
├── schemas/
│   └── aqa-schema.json
├── badges/
│   ├── aqa-basic.svg
│   ├── aqa-standard.svg
│   ├── aqa-full.svg
│   └── aqa-shield.svg
├── .well-known/
│   └── aqa-updates.json
├── robots.txt
├── sitemap.xml
├── favicon.ico
└── CLAUDE.md
```

## Regles de design
- Sobre, technique, credible — penser Schema.org, jsonld.com, ou les RFC de l'IETF
- Fond blanc, typographie propre (Inter ou system fonts), code au centre
- AUCUN emoji, AUCUNE icone fantaisiste, AUCUN gradient flashy
- Palette : blanc (#ffffff), gris fonce (#1a1a2e) pour le texte, bleu sobre (#2563eb) pour les liens et accents, gris clair (#f3f4f6) pour les blocs de code
- Mobile-first, responsive
- Le site doit charger en moins de 1 seconde

## Regles imperatives
- Le fichier ns/context.jsonld est le fichier le plus important du projet
- AUCUNE mention commerciale d'AI Labs Audit
- Tout le contenu est en anglais
- Seul lien commercial autorise dans le footer : "Created by Davy Abderrahman — AI Labs Solutions"

## Etat d'avancement
- [x] Fichier CLAUDE.md cree
- [x] Structure de dossiers creee
- [x] context.jsonld recupere et adapte depuis le repo GitHub
- [x] aqa-schema.json recupere depuis le repo GitHub
- [x] Landing page index.html creee
- [x] style.css cree
- [x] 4 badges SVG crees
- [x] robots.txt cree
- [x] sitemap.xml cree
- [x] .well-known/aqa-updates.json cree
- [ ] Test local verifie
- [ ] Pret pour deploiement FTP
