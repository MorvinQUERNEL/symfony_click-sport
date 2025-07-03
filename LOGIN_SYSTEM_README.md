# Système de Connexion Refait - Click & Sport

## ✅ **Système de connexion complètement refait et simplifié**

### **Changements effectués :**

1. **CSRF désactivé** pour éviter les problèmes de token
2. **Formulaire simplifié** sans champs cachés complexes
3. **Configuration de sécurité épurée**
4. **Nouvelle commande** pour créer un utilisateur de test

---

## **Installation et Test**

### **1. Vider le cache Symfony**
```bash
php bin/console cache:clear
```

### **2. Créer un utilisateur de test**
```bash
php bin/console app:create-test-user
```

### **3. Tester la connexion**
- Aller sur `/login`
- Utiliser les identifiants :
  - **Email** : `test@example.com`
  - **Mot de passe** : `password123`

---

## **Structure du système**

### **Fichiers modifiés :**
- `app/config/packages/security.yaml` - CSRF désactivé
- `app/templates/security/login.html.twig` - Formulaire simplifié
- `app/src/Command/CreateTestUserCommand.php` - Nouvelle commande

### **Configuration de sécurité :**
```yaml
form_login:
    login_path: app_login
    check_path: app_login
    enable_csrf: false  # ← CSRF désactivé
```

### **Formulaire de connexion :**
- Champ email : `_username`
- Champ mot de passe : `_password`
- Action : `{{ path('app_login') }}`
- Méthode : `POST`

---

## **Fonctionnalités**

✅ **Connexion simple** sans CSRF  
✅ **Gestion des erreurs** en français  
✅ **Utilisateur de test** automatique  
✅ **Redirection** vers l'accueil après connexion  
✅ **Formulaire responsive** et moderne  

---

## **Dépannage**

### **Si la connexion ne fonctionne toujours pas :**

1. **Vérifier que l'utilisateur existe en base :**
   ```bash
   php bin/console doctrine:query:sql "SELECT email FROM users WHERE email = 'test@example.com'"
   ```

2. **Recréer l'utilisateur :**
   ```bash
   php bin/console app:create-test-user
   ```

3. **Vérifier les logs Symfony :**
   ```bash
   tail -f var/log/dev.log
   ```

4. **Tester avec un autre navigateur** ou mode incognito

---

## **Prochaines étapes**

Une fois la connexion fonctionnelle, vous pourrez :
- Ajouter la déconnexion dans la navigation
- Créer un profil utilisateur
- Ajouter la gestion des rôles
- Réactiver CSRF si nécessaire 