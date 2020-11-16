<?php

$smarty = new MySmarty();

$userAdmin = new User();
$userAdmin->user_id = 'ADM';
$userAdmin->nom = 'admin';
$userAdmin->login = 'admin';
$userAdmin->password = 'df5b909019c9b1659e86e0d6bf8da81d6fa3499e';
$userAdmin->visible_planning = 'non';
$userAdmin->couleur = '000000';
$userAdmin->droits = '["users_manage_all", "projects_manage_all", "projectgroups_manage_all", "tasks_modify_all", "tasks_view_all_projects", "lieux_all", "ressources_all", "parameters_all", "stats_users", "stats_projects", "audit_restore"]';
$userAdmin->cle = MD5(RAND());
$userAdmin->notifications = 'oui';
$userAdmin->login_actif = 'oui';
$userAdmin->date_creation = date('Y-m-d H:i:s');
$userAdmin->db_save();


$statut1 = new Status();
$statut1->status_id = 'todo';
$statut1->nom = $smarty->getConfigVars('winProjet_statutAFaire');
$statut1->affichage = 'tp';
$statut1->pourcentage = 0;
$statut1->priorite = 1;
$statut1->defaut = 1;
$statut1->couleur = '4dabf5';
$statut1->db_save();

$statut2 = new Status();
$statut2->status_id = 'progress';
$statut2->nom = $smarty->getConfigVars('winProjet_statutEnCours');
$statut2->affichage = 'tp';
$statut2->pourcentage = 50;
$statut2->priorite = 2;
$statut2->defaut = 1;
$statut2->couleur = 'ffeb3b';
$statut2->gras = 1;
$statut2->souligne = 1;
$statut2->db_save();

$statut3 = new Status();
$statut3->status_id = 'done';
$statut3->nom = $smarty->getConfigVars('winProjet_statutFait');
$statut3->affichage = 'tp';
$statut3->pourcentage = 100;
$statut3->priorite = 3;
$statut3->defaut = 1;
$statut3->couleur = '1fcb27';
$statut3->barre = 1;
$statut3->db_save();

$statut4 = new Status();
$statut4->status_id = 'abort';
$statut4->nom = $smarty->getConfigVars('winProjet_statutAbandon');
$statut4->affichage = 'tp';
$statut4->pourcentage = 0;
$statut4->priorite = 4;
$statut4->defaut = 1;
$statut4->couleur = 'ff0f0f';
$statut4->italique = 1;
$statut4->db_save();

$statut5 = new Status();
$statut5->status_id = 'archive';
$statut5->nom = $smarty->getConfigVars('winProjet_statutArchive');
$statut5->affichage = 'tp';
$statut5->pourcentage = 100;
$statut5->priorite = 5;
$statut5->defaut = 1;
$statut5->couleur = '865ece';
$statut5->barre = 1;
$statut5->italique = 1;
$statut5->db_save();

$ug1 = new User_groupe();
$ug1->nom = $smarty->getConfigVars('demo_equipe_1');
$ug1->db_save();

$ug2 = new User_groupe();
$ug2->nom = $smarty->getConfigVars('demo_equipe_2');
$ug2->db_save();



$g1 = new Groupe();
$g1->nom = $smarty->getConfigVars('demo_groupe_1');
$g1->db_save();

$g2 = new Groupe();
$g2->nom = $smarty->getConfigVars('demo_groupe_2');
$g2->db_save();

$g3 = new Groupe();
$g3->nom = $smarty->getConfigVars('demo_groupe_3');
$g3->db_save();


$userPublic = new User();
$userPublic->user_id = 'publicspl';
$userPublic->nom = 'Guest';
$userPublic->login = NULL;
$userPublic->visible_planning = 'non';
$userPublic->couleur = '000000';
$userPublic->cle = MD5(RAND());
$userPublic->notifications = 'non';
$userPublic->login_actif = 'oui';
$userPublic->date_creation = date('Y-m-d H:i:s');
$userPublic->db_save();

$user1 = new User();
$user1->user_id = 'user1';
$user1->nom = $smarty->getConfigVars('demo_user_1');
$user1->user_groupe_id = $ug1->user_groupe_id;
$user1->visible_planning = 'oui';
$user1->couleur = 'ffeb3b';
$user1->droits = '["","","","tasks_readonly","tasks_view_all_projects","tasks_view_all_users","","","","","",""]';
$user1->cle = MD5(RAND());
$user1->notifications = 'oui';
$user1->login_actif = 'oui';
$user1->date_creation = date('Y-m-d H:i:s');
$user1->db_save();

$user2 = new User();
$user2->user_id = 'user2';
$user2->nom = $smarty->getConfigVars('demo_user_2');
$user2->user_groupe_id = $ug1->user_groupe_id;
$user2->visible_planning = 'oui';
$user2->couleur = '4dabf5';
$user2->droits = '["","","","tasks_readonly","tasks_view_all_projects","tasks_view_all_users","","","","","",""]';
$user2->cle = MD5(RAND());
$user2->notifications = 'oui';
$user2->login_actif = 'oui';
$user2->date_creation = date('Y-m-d H:i:s');
$user2->db_save();

$user3 = new User();
$user3->user_id = 'user3';
$user3->nom = $smarty->getConfigVars('demo_user_3');
$user3->user_groupe_id = $ug2->user_groupe_id;
$user3->visible_planning = 'oui';
$user3->couleur = '1fcb27';
$user3->droits = '["","","","tasks_readonly","tasks_view_all_projects","tasks_view_all_users","","","","","",""]';
$user3->cle = MD5(RAND());
$user3->notifications = 'oui';
$user3->login_actif = 'oui';
$user3->date_creation = date('Y-m-d H:i:s');
$user3->db_save();


$projet1 = new Projet();
$projet1->projet_id = 'test1';
$projet1->nom = $smarty->getConfigVars('demo_projet_1');
$projet1->couleur = 'ff784e';
$projet1->statut = $statut1->status_id;
$projet1->groupe_id = $g1->groupe_id;
$projet1->createur_id = $userAdmin->user_id;
$projet1->db_save();

$projet2 = new Projet();
$projet2->projet_id = 'test2';
$projet2->nom = $smarty->getConfigVars('demo_projet_2');
$projet2->couleur = '5bf3d0';
$projet2->statut = $statut2->status_id;
$projet2->groupe_id = $g1->groupe_id;
$projet2->createur_id = $userAdmin->user_id;
$projet2->db_save();

$projet3 = new Projet();
$projet3->projet_id = 'test3';
$projet3->nom = $smarty->getConfigVars('demo_projet_3');
$projet3->couleur = 'e6c843';
$projet3->statut = $statut1->status_id;
$projet3->groupe_id = $g2->groupe_id;
$projet3->createur_id = $userAdmin->user_id;
$projet3->db_save();


$tache = new Periode();
$tache->projet_id = $projet1->projet_id;
$tache->user_id = $user1->user_id;
$tache->link_id = uniqid(mt_rand());
$dateTmp = new Datetime();
$dateTmp->modify('+2 days');
$tache->date_debut = $dateTmp->format('Y-m-d');
$dateTmp->modify('+9 days');
$tache->date_fin = $dateTmp->format('Y-m-d');
$tache->duree = NULL;
$tache->duree_details = NULL;
$tache->titre = $smarty->getConfigVars('demo_tache_1');
$tache->statut_tache = $statut1->status_id;
$tache->createur_id = $userAdmin->user_id;
$tache->date_creation = date('Y-m-d H:i:s');
$tache->db_save();

$tache = new Periode();
$tache->projet_id = $projet2->projet_id;
$tache->user_id = $user1->user_id;
$tache->link_id = uniqid(mt_rand());
$dateTmp = new Datetime();
$dateTmp->modify('+5 days');
$tache->date_debut = $dateTmp->format('Y-m-d');
$dateTmp->modify('+4 days');
$tache->date_fin = $dateTmp->format('Y-m-d');
$tache->duree = NULL;
$tache->duree_details = NULL;
$tache->titre = $smarty->getConfigVars('demo_tache_2');
$tache->statut_tache = $statut2->status_id;
$tache->createur_id = $userAdmin->user_id;
$tache->date_creation = date('Y-m-d H:i:s');
$tache->db_save();

$tache = new Periode();
$tache->projet_id = $projet1->projet_id;
$tache->user_id = $user2->user_id;
$tache->link_id = uniqid(mt_rand());
$dateTmp = new Datetime();
$dateTmp->modify('+1 days');
$tache->date_debut = $dateTmp->format('Y-m-d');
$tache->date_fin = NULL;
$tache->duree = '04:55:00';
$tache->duree_details = '08:35:00;13:30:00';
$tache->titre = NULL;
$tache->statut_tache = $statut3->status_id;
$tache->createur_id = $userAdmin->user_id;
$tache->date_creation = date('Y-m-d H:i:s');
$tache->db_save();

$tache = new Periode();
$tache->projet_id = $projet3->projet_id;
$tache->user_id = $user2->user_id;
$tache->link_id = uniqid(mt_rand());
$dateTmp = new Datetime();
$dateTmp->modify('+1 days');
$tache->date_debut = $dateTmp->format('Y-m-d');
$tache->date_fin = NULL;
$tache->duree = '00:20:00';
$tache->duree_details = '15:00:00;15:20:00';
$tache->titre = NULL;
$tache->statut_tache = $statut1->status_id;
$tache->createur_id = $userAdmin->user_id;
$tache->date_creation = date('Y-m-d H:i:s');
$tache->db_save();

$tache = new Periode();
$tache->projet_id = $projet3->projet_id;
$tache->user_id = $user3->user_id;
$tache->link_id = uniqid(mt_rand());
$dateTmp = new Datetime();
$dateTmp->modify('+4 days');
$tache->date_debut = $dateTmp->format('Y-m-d');
$tache->date_fin = NULL;
$tache->duree = '04:00:00';
$tache->duree_details = 'AM';
$tache->titre = NULL;
$tache->statut_tache = $statut2->status_id;
$tache->createur_id = $userAdmin->user_id;
$tache->date_creation = date('Y-m-d H:i:s');
$tache->db_save();

$tache = new Periode();
$tache->projet_id = $projet2->projet_id;
$tache->user_id = $user3->user_id;
$tache->link_id = uniqid(mt_rand());
$dateTmp = new Datetime();
$dateTmp->modify('+4 days');
$tache->date_debut = $dateTmp->format('Y-m-d');
$dateTmp->modify('+4 days');
$tache->date_fin = $dateTmp->format('Y-m-d');
$tache->duree = NULL;
$tache->duree_details = NULL;
$tache->titre = NULL;
$tache->statut_tache = $statut1->status_id;
$tache->createur_id = $userAdmin->user_id;
$tache->date_creation = date('Y-m-d H:i:s');
$tache->db_save();


?>