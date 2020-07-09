# enjoyBooks

Projet d’application autour des livres
Stack
Back-end : Symfony 5, RabbitMq, Docker 
Front-end : Twig / bootstrap

-	Récupérer une base de données externe de livres / auteurs
-	Stocker : Auteurs, livres
-	Système d’utilisateur
o	Création de compte (via un compte google)
o	Connexion / Déconnexion 
-	Un utilisateur peut s’abonner à un auteur
o	Il reçoit une notification par mail pour chaque MAJ sur l’auteur (Nouveau livre,…)
-	Faire tourner un cron qui récupère les MAJ des données (auteurs / livres)
o	Ajouter les nouveautés en BDD
o	Envoyer une notification aux abonnés 
