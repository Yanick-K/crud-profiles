## Stack
- **Laravel 11**
- **PHP 8.3**
- **Vue 3**
- **Vite**
- **Node 20**
- **SQLite** (optionnel)

> ⚠️ **Prérequis** : PHP 8.3 et Node 20 sont requis.

## Instructions 

```bash
composer install && cp .env.example .env &&  php artisan migrate --seed && npm install && npm run build
```

```bash
php artisan key:generate && php artisan storage:link && php artisan serve
```

### Création d'un administrateur

Pour créer un compte admin, il faut lancer la commande suivante :

```bash
php artisan admin:create
```

Puis suivre les intructions

---

Documentation pour la liste des endpoints
https://documenter.getpostman.com/view/10094201/2sAYdfoVzD
