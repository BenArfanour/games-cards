# Installation & utilisation (via Makefile)

## Prérequis
- **Docker** + **Docker Compose** installés (WSL2 OK).

<<<<<<< HEAD
## 1) Lancer l'environnement
=======
## 1) Lancer l’environnement
>>>>>>> ea1b950 (Initial)
```bash
make up

Build + start des containers (php-fpm + nginx).
Ouvre : http://localhost:8080


2) Installer les dépendances PHP (dans le conteneur)
make install


<<<<<<< HEAD
3) Lancer l'UI Web
=======
3) Lancer l’UI Web
>>>>>>> ea1b950 (Initial)
Aller sur : http://localhost:8080/cards

4) Lancer la CLI
make game

5) Lancer les tests:
make test     # PHPUnit

(Optionnel) Qualité :
make stan     # PHPStan
make cs       # Vérif code style
make fix      # Auto-fix code style


6) Ouvrir un shell dans le conteneur PHP (si besoin):
make sh


Raccourcis Makefile disponibles

make up : build & start containers

make down : stop & remove containers

make install : composer install dans le conteneur

make test : exécuter PHPUnit

make stan : PHPStan 

make cs : check code style 

make fix : auto-fix CS 

make sh : shell dans le conteneur PHP
