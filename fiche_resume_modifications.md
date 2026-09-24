# Fiche de résumé des modifications

## 1. Objectif du projet

Le projet Laravel a été enrichi avec plusieurs exemples pour apprendre à manipuler:

- les routes
- les contrôleurs CRUD
- Eloquent
- Query Builder
- les migrations
- les factories
- les seeders
- les relations entre modèles

Cette fiche résume les changements faits, avec une explication simple pour débutant.

## 2. Correction de la page d’accueil

Au départ, la racine du site affichait une erreur 404.

La solution a été d’ajouter une route d’accueil dans `routes/web.php`:

```php
Route::view('/', 'welcome');
```

### Idée à retenir

Une route relie une URL à une action Laravel. Ici, `/` affiche la vue `welcome`.

## 3. CRUD du modèle Test / Classe

Le projet contient un modèle `Classe` qui pointe vers la table `tests`.

### Modèle

Dans `app/Models/Classe.php`, on a ajouté:

- `protected $table = 'tests';`
- `protected $fillable = ['champs1', 'champs2'];`

### Pourquoi

Laravel cherche normalement une table nommée comme le modèle au pluriel. Ici, on force la table `tests`.

### Contrôleur

Dans `app/Http/Controllers/ClasseController.php`, on a mis en place un CRUD complet:

- `index()` pour lister les enregistrements
- `create()` pour afficher le formulaire
- `store()` pour créer un enregistrement
- `show()` pour afficher un enregistrement
- `edit()` pour modifier un enregistrement
- `update()` pour enregistrer la modification
- `destroy()` pour supprimer

### Vues

Les vues CRUD ont été utilisées dans:

- `resources/views/tests/index.blade.php`
- `resources/views/tests/create.blade.php`
- `resources/views/tests/show.blade.php`
- `resources/views/tests/edit.blade.php`

### Routes

La ressource a été enregistrée avec:

```php
Route::resource('tests', App\Http\Controllers\ClasseController::class);
```

### Idée à retenir

Le mot `resource` crée automatiquement les routes CRUD classiques.

## 4. Débogage avec dd

Pour voir rapidement le contenu de la table, l’action `index()` du contrôleur a été temporairement modifiée avec `dd()`.

Exemple:

```php
dd(Classe::all());
```

### Idée à retenir

- `dd()` = affiche une variable et arrête le programme
- `dump()` = affiche une variable sans arrêter le programme

## 5. Passage à Query Builder

Le fichier `app/Models/TestQueryBuilder.php` a été utilisé pour montrer une approche sans Eloquent.

### Différence entre Eloquent et Query Builder

- Eloquent travaille avec des modèles PHP
- Query Builder travaille directement avec la table SQL via `DB`

### Exemple Query Builder

```php
DB::table('tests')->get();
```

### Correction importante

Une méthode comme `DB::select($query)->get()` n’est pas correcte.

La bonne logique est:

```php
DB::select($query);
```

### CRUD Query Builder

Le `ClasseController` a ensuite été adapté pour fonctionner aussi avec Query Builder via `TestQueryBuilder`.

## 6. Création de la classe Absence

Une nouvelle entité `Absence` a été créée pour gérer les absences d’un salarié.

L’idée était de pouvoir enregistrer, lire, modifier et supprimer des absences comme pour un petit module de gestion du personnel.

### Ce que représente une absence

Une absence contient les informations suivantes:

- le salarié concerné
- la date de début
- la date de fin
- le motif de l’absence

### Pourquoi créer une classe dédiée

Créer une classe `Absence` permet de séparer les données des absences des autres données du projet.

Cela rend le code plus clair et plus facile à maintenir.

### Fichiers créés

- `app/Models/Absence.php`
- `app/Http/Controllers/AbsenceController.php`
- `database/migrations/2026_09_04_140304_create_absences_table.php`
- `database/factories/AbsenceFactory.php`
- `database/seeders/AbsenceSeeder.php`

### Rôle de chaque fichier

- `Absence.php` représente la table `absences` dans le code PHP.
- `AbsenceController.php` gère les actions comme afficher, créer, modifier et supprimer.
- la migration crée la table dans la base de données.
- la factory fabrique de fausses absences pour les tests.
- le seeder remplit la base avec des absences de démonstration.

### Champs de la table absences

La table contient:

- `user_id`
- `date_debut`
- `date_fin`
- `motif`
- `created_at`
- `updated_at`

### Ce que fait la migration

La migration crée la table `absences` avec une clé étrangère `user_id`.

Exemple du code important:

```php
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

Cela veut dire:

- `user_id` pointe vers la table `users`
- si un utilisateur est supprimé, ses absences sont supprimées aussi

### Pourquoi les dates sont en type `date`

Les champs `date_debut` et `date_fin` utilisent le type `date` car on veut stocker uniquement une date, pas une heure précise.

### Idée à retenir

Une migration sert à créer ou modifier la structure d’une table.

## 7. Relation entre User et Absence

Le modèle `User` a reçu une relation:

```php
public function absences()
{
    return $this->hasMany(Absence::class);
}
```

Le modèle `Absence` a la relation inverse:

```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

### Explication simple

- un utilisateur peut avoir plusieurs absences
- une absence appartient à un seul utilisateur

### Comment lire cette relation

- `hasMany()` signifie "un utilisateur possède plusieurs absences"
- `belongsTo()` signifie "une absence appartient à un utilisateur"

### Exemple d’utilisation

```php
$user = User::find(1);
$absences = $user->absences;
```

Ici, on récupère toutes les absences du salarié dont l’identifiant est `1`.

## 8. Controller Absence

Le contrôleur `AbsenceController` a été préparé pour gérer le CRUD.

Il sert d’intermédiaire entre la base de données, les règles de validation et la réponse envoyée à l’écran.

### Méthodes principales

- `index()` affiche la liste des absences
- `create()` prépare les données pour le formulaire
- `store()` enregistre une absence
- `show()` affiche une absence avec son salarié
- `edit()` prépare la modification
- `update()` met à jour l’absence
- `destroy()` supprime l’absence

### Ce que fait chaque méthode

- `index()` récupère toutes les absences avec le salarié lié.
- `create()` récupère la liste des utilisateurs pour choisir le salarié.
- `store()` vérifie les données puis crée une nouvelle absence.
- `show()` affiche une absence précise avec son salarié.
- `edit()` affiche les données à modifier.
- `update()` enregistre les modifications.
- `destroy()` supprime l’absence.

### Validation

On vérifie notamment:

- que `user_id` existe dans `users`
- que `date_fin` est postérieure ou égale à `date_debut`
- que `motif` est rempli

### Exemple de validation

```php
$validated = $request->validate([
    'user_id' => ['required', 'exists:users,id'],
    'date_debut' => ['required', 'date'],
    'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
    'motif' => ['required', 'string', 'max:255'],
]);
```

### Pourquoi valider

La validation évite d’enregistrer des données fausses ou incomplètes dans la base.

## 9. Factory et Seeder Absence

### Factory

La factory sert à générer des données de test automatiquement.

Dans ce projet, elle crée une absence avec:

- un utilisateur choisi automatiquement
- une date de début
- une date de fin
- un motif aléatoire

### Seeder

Le seeder sert à remplir la base avec des données de départ.

Il permet d’avoir des exemples prêts à l’emploi quand on teste le projet.

Exemple:

```php
Absence::factory()->count(5)->create();
```

### Idée à retenir

Une factory crée des faux enregistrements.
Un seeder insère ces données dans la base.

### Pourquoi c’est utile

Sans données de test, il est difficile de vérifier si l’affichage et le CRUD fonctionnent correctement.

## 10. Routes ajoutées pour Absence

Une route ressource a été ajoutée:

```php
Route::resource('absences', App\Http\Controllers\AbsenceController::class);
```

Cela crée automatiquement:

- `/absences`
- `/absences/create`
- `/absences/{absence}`
- `/absences/{absence}/edit`

et les actions associées.

### Ce que ça apporte

Avec `Route::resource`, on n’a pas besoin d’écrire manuellement chaque route CRUD.

## 11. Résumé pédagogique rapide

### Ce qu’il faut retenir

1. Une route envoie vers un contrôleur.
2. Un contrôleur gère la logique.
3. Un modèle représente une table.
4. Une migration crée ou modifie une table.
5. Une factory fabrique des données.
6. Un seeder remplit la base.
7. Une relation lie deux modèles.

### Eloquent vs Query Builder

- Eloquent est plus orienté objet et plus simple pour débuter.
- Query Builder est plus direct et plus proche de SQL.

## 12. Exemples utiles

### Voir toutes les absences d’un utilisateur

```php
$user->absences;
```

### Voir une absence avec son salarié

```php
Absence::with('user')->get();
```

### Afficher les données avec dd

```php
dd(Absence::all());
```

## 13. Conclusion

Le projet sert maintenant de support d’apprentissage pour:

- CRUD Laravel
- Eloquent
- Query Builder
- relations entre modèles
- migrations
- seeders
- factories

Cette base permet de continuer facilement avec des pages Blade ou une API JSON.

## --------------------------------------------------------------------------------------------
## --------------------------------------------------------------------------------------------
## --------------------------------------------------------------------------------------------
## --------------------------------------------------------------------------------------------
## --------------------------------------------------------------------------------------------
## --------------------------------------------------------------------------------------------
## --------------------------------------------------------------------------------------------
## --------------------------------------------------------------------------------------------
## --------------------------------------------------------------------------------------------

## 14. Commandes utilisées dans l’ordre

Voici les commandes utilisées pendant le travail, avec leur contexte et leur rôle.

### 1. `php -v`

Contexte: vérifier si PHP était disponible dans le terminal.

Utilité: afficher la version de PHP installée.

### 2. `composer -V`

Contexte: vérifier si Composer était installé.

Utilité: confirmer que l’outil de gestion des dépendances PHP fonctionne.

### 3. `composer require laravel/boost --dev`

Contexte: installation demandée par l’environnement Laravel du projet.

Utilité: ajouter Laravel Boost en dépendance de développement.

### 4. `php artisan boost:install`

Contexte: initialisation de Laravel Boost dans le projet.

Utilité: installer les consignes spécifiques du projet.

### 5. `php artisan route:list`

Contexte: diagnostiquer les routes et vérifier le 404 sur la racine.

Utilité: afficher toutes les routes enregistrées par Laravel.

### 6. `curl.exe -I http://test2.test/`

Contexte: tester la réponse HTTP du site local dans le navigateur.

Utilité: vérifier si l’URL renvoie un `200`, un `404` ou une autre erreur.

### 7. `php artisan make:migration update_tests_table --table=tests`

Contexte: créer une migration de mise à jour pour la table `tests`.

Utilité: générer un fichier de migration `Schema::table(...)`.

### 8. `php artisan make:model Absence -m`

Contexte: création du module `Absence`.

Utilité: générer le modèle `Absence` et sa migration.

### 9. `php artisan make:controller AbsenceController --resource`

Contexte: création du contrôleur pour le CRUD des absences.

Utilité: générer les méthodes CRUD de base.

### 10. `php artisan make:factory AbsenceFactory --model=Absence`

Contexte: préparer des données de test pour `Absence`.

Utilité: générer une factory liée au modèle `Absence`.

### 11. `php artisan make:seeder AbsenceSeeder`

Contexte: préparer le remplissage automatique de la table `absences`.

Utilité: générer un seeder dédié.

### 12. `php artisan migrate --pretend`

Contexte: vérifier la migration `absences` sans modifier réellement la base.

Utilité: afficher le SQL qui serait exécuté.

### 13. `php artisan route:list --path=tests`

Contexte: valider les routes CRUD du module `tests`.

Utilité: vérifier que le contrôleur `ClasseController` est bien branché.

### 14. `php artisan route:list --path=absences`

Contexte: valider les routes CRUD du module `absences`.

Utilité: confirmer que `AbsenceController` est bien enregistré.

### 15. `php artisan route:list --path=users`

Contexte: tester la route ajoutée pour afficher les absences d’un salarié.

Utilité: vérifier la route `users.show`.

### 16. `php artisan make:controller UserController`

Contexte: création du contrôleur pour afficher un salarié et ses absences.

Utilité: générer un contrôleur dédié à la vue `users.show`.

### 17. `php artisan make:factory AbsenceFactory --model=Absence`

Contexte: créer des données fictives pour les absences.

Utilité: produire des absences de test automatiquement.

### 18. `php artisan make:seeder AbsenceSeeder`

Contexte: peupler la table `absences` avec des exemples.

Utilité: fournir des données de démonstration.

### 19. `php artisan make:model Absence -m`

Contexte: démarrage du module Absence.

Utilité: générer le modèle et la migration associés.

### 20. `php artisan make:controller AbsenceController --resource`

Contexte: construire le CRUD d’Absence.

Utilité: obtenir les méthodes standard du contrôleur.

### 21. `php artisan make:factory AbsenceFactory --model=Absence`

Contexte: compléter le module avec une factory.

Utilité: fabriquer des enregistrements de test.

### 22. `php artisan make:seeder AbsenceSeeder`

Contexte: compléter le module avec un seeder.

Utilité: remplir la base avec des exemples.

### 23. `php artisan route:list --path=absences`

Contexte: contrôle final du module Absence.

Utilité: vérifier que toutes les routes CRUD sont visibles.

### 24. `php artisan route:list --path=users`

Contexte: contrôle final du module User/Absence.

Utilité: vérifier l’accès à la page d’un salarié avec ses absences.

### 25. `php artisan route:list --path=tests`

Contexte: contrôle final du module Tests/Classe.

Utilité: vérifier que le CRUD principal reste fonctionnel.

### 26. `php artisan route:list --path=/`

Contexte: validation de la page d’accueil du projet.

Utilité: confirmer que la racine du site répond correctement.

### 27. `php artisan make:migration update_tests_table --table=tests`

Contexte: création d’une migration de modification de la table `tests`.

Utilité: préparer une migration de mise à jour plutôt qu’une création.

### 28. `php artisan make:migration update_tests_table --table=tests` puis suppression du fichier

Contexte: test de création puis suppression de cette migration.

Utilité: corriger une migration devenue inutile.

### 29. `php artisan route:list --path=tests` et `curl.exe -I http://test2.test/tests`

Contexte: vérifier la disponibilité de la page CRUD tests.

Utilité: confirmer que la page répond en `200 OK`.

### 30. `php artisan route:list --path=absences`

Contexte: vérifier la ressource Absence après ajout du module.

Utilité: confirmer l’enregistrement des routes CRUD.

### 31. `php artisan route:list --path=users`

Contexte: vérifier la route du `UserController`.

Utilité: valider l’accès à la page qui affiche les absences d’un salarié.

### 32. `php artisan route:list --path=absences` après ajout du CRUD

Contexte: validation finale.

Utilité: vérifier que les routes `index`, `show`, `store`, `edit`, `update`, `destroy` sont bien générées.
