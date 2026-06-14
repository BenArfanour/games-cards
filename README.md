# Games Cards

Démonstration Symfony 6.4 de distribution et de tri de cartes à jouer — interface web et commande CLI, entièrement conteneurisée avec Docker.

## Fonctionnalités

- **Distribution aléatoire** de mains à partir d'un jeu standard de 52 cartes
- **Tri personnalisable** selon un ordre aléatoire de couleurs et de rangs
- **Interface web** sur `/cards` avec affichage des mains triée et non triée
- **Commande CLI** `app:deal-cards` pour tester le moteur en terminal
- **Architecture hexagonale** légère : Domain, Application, Infrastructure, UI

## Stack technique

| Composant | Version |
|-----------|---------|
| PHP | 8.2 (Docker) |
| Symfony | 6.4 |
| Serveur web | Nginx 1.25 + PHP-FPM |
| Tests | PHPUnit 11 |
| Qualité | PHPStan 2, PHP-CS-Fixer |

## Prérequis

- [Docker](https://docs.docker.com/get-docker/) et Docker Compose
- WSL2 supporté sous Windows

## Démarrage rapide

```bash
# 1. Démarrer l'environnement (build + containers)
make up

# 2. Installer les dépendances PHP
make install

# 3. Ouvrir l'application
#    → http://localhost:8080/cards
```

Pour distribuer une main via le terminal :

```bash
make game
```

## Commandes Makefile

| Commande | Description |
|----------|-------------|
| `make up` | Build et démarrage des containers (php-fpm + nginx) |
| `make down` | Arrêt et suppression des containers et volumes |
| `make install` | `composer install` dans le container PHP |
| `make game` | Lance la commande CLI `app:deal-cards` |
| `make test` | Exécute la suite PHPUnit |
| `make stan` | Analyse statique PHPStan |
| `make cs` | Vérification du code style (dry-run) |
| `make fix` | Correction automatique du code style |
| `make sh` | Ouvre un shell bash dans le container PHP |
| `make composer cmd="…"` | Exécute une commande Composer arbitraire |

## Configuration

Copiez `.env.example` vers `.env.local` pour personnaliser votre environnement local :

```bash
cp .env.example .env.local
# Éditez .env.local et définissez un APP_SECRET unique
```

Les variables d'environnement par défaut sont dans `.env`. Le fichier `.env.local` (non versionné) surcharge ces valeurs.

> **Important :** ne commitez jamais de secrets réels. Utilisez `.env.local` pour vos valeurs personnelles.

Pour les customisations Docker locales, copiez `docker-compose.override.dist.yml` vers `docker-compose.override.yml` (gitignored).

## Structure du projet

```
src/
├── Domain/           # Modèles métier (Card, Hand, Suit, Rank)
├── Application/      # Services et ports (HandDealer, HandSorter, …)
├── Infrastructure/   # Implémentations techniques (PhpRandomizer)
└── UI/
    ├── Http/         # Contrôleur web (CardsController)
    └── Console/      # Commande CLI (DealCardsCommand)
config/
├── routes/           # Définition des routes YAML
└── packages/         # Configuration Symfony
tests/
├── Unit/             # Tests unitaires
└── Functional/       # Tests fonctionnels (HTTP, CLI)
docker/
├── php/Dockerfile    # Image PHP-FPM
└── nginx/            # Configuration Nginx
```

## Qualité de code

```bash
make test    # Tests unitaires et fonctionnels (25 tests)
make stan    # Analyse statique PHPStan (niveau 6 + extension Symfony)
make cs      # Vérifier le style
make fix     # Corriger le style automatiquement
```

PHPUnit est configuré avec `failOnDeprecation=true` pour détecter les dépréciations Symfony dès les tests. Les rapports de couverture sont générés dans `var/coverage/` lorsque Xdebug ou PCOV est disponible.

## Licence

Projet propriétaire — usage interne / démonstration.
