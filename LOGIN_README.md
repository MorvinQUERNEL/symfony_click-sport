# Système de Connexion - Click & Sport

## Fonctionnalités

✅ **Page de connexion** avec formulaire moderne et responsive  
✅ **Contrôleur de sécurité** gérant l'authentification  
✅ **Lien "Connectez-vous"** dans la navigation  
✅ **Configuration de sécurité** Symfony  
✅ **Utilisateur de test** pour les tests  

## Installation et Configuration

### 1. Créer un utilisateur de test

Pour tester la connexion, créez un utilisateur de test avec la commande :

```bash
php bin/console app:create-user
```

Cela créera un utilisateur avec :
- **Email** : test@example.com
- **Mot de passe** : password123

### 2. Vérifier la configuration

Assurez-vous que la base de données est à jour :

```bash
php bin/console doctrine:migrations:migrate
```

### 3. Tester la connexion

1. Allez sur la page d'accueil
2. Cliquez sur "Connectez-vous" dans la navigation
3. Utilisez les identifiants de test ci-dessus

## Structure des fichiers

```
app/
├── src/
│   ├── Controller/
│   │   ├── SecurityController.php     # Gestion de l'authentification
│   │   └── HomeController.php         # Page d'accueil
│   ├── Entity/
│   │   └── Users.php                  # Entité utilisateur
│   └── Command/
│       └── CreateUserCommand.php      # Commande pour créer un utilisateur
├── templates/
│   ├── security/
│   │   └── login.html.twig           # Template de connexion
│   └── home/
│       └── index.html.twig           # Page d'accueil avec lien de connexion
└── config/
    └── packages/
        └── security.yaml             # Configuration de sécurité
```

## Fonctionnalités du formulaire de connexion

- **Design moderne** avec gradient et animations
- **Responsive** pour mobile et desktop
- **Gestion des erreurs** d'authentification
- **Validation** des champs email et mot de passe
- **Lien de retour** vers l'accueil
- **Liens** pour création de compte et mot de passe oublié

## Sécurité

- **CSRF protection** activée
- **Hachage des mots de passe** automatique
- **Authentification par formulaire** Symfony
- **Gestion des sessions** sécurisée

## Prochaines étapes

Pour compléter le système, vous pourriez ajouter :
- Page d'inscription
- Page de mot de passe oublié
- Profil utilisateur
- Déconnexion dans la navigation
- Gestion des rôles utilisateur 