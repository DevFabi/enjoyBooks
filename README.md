# :books: enjoyBooks :books:

Projet d’application autour des livres  
Stack  
Back-end : Symfony 5, RabbitMq :rabbit:, Docker :whale:, ElasticSearch :bar_chart:  
Front-end : Twig / bootstrap  
  
-	Récupérer une base de données externe de livres / auteurs  (Google API Books)  
-	Stocker : Auteurs, livres  
-	Système d’utilisateur :man:  
o	Création de compte (via un compte google)  
o	Connexion / Déconnexion   
-	Un utilisateur peut s’abonner à un auteur  
o	Il reçoit une notification par mail :email: pour chaque MAJ sur l’auteur (Nouveau livre,…)  
-	Faire tourner un cron qui récupère les MAJ des données (auteurs / livres :book:)  
o	Ajouter les nouveautés en BDD  
o	Envoyer une notification aux abonnés :clock7:   
- Espace administrateur (Modifier, ajouter, supprimer : Livres, auteurs, utilisateurs)  
- Systeme de recherche par auteur et/ou nom de livre  
