
CREATE TABLE `planning_config` (
  `cle` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `valeur` varchar(1024) COLLATE latin1_general_ci DEFAULT NULL,
  `commentaire` text COLLATE latin1_general_ci,
  PRIMARY KEY (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

INSERT INTO `planning_config` VALUES('CURRENT_VERSION', '1.50.02', 'Internal key for auto upgrade control');
INSERT INTO `planning_config` VALUES('PLANNING_PAGES', '1,5,10,20,50,100', 'rows per page in the planning');
INSERT INTO `planning_config` VALUES('PROJECT_COLORS_POSSIBLE', '', 'color choice limitation for planner (empty for no limit). Exemple :#ff0000,#aa8811,#446622');
INSERT INTO `planning_config` VALUES('DEFAULT_NB_MONTHS_DISPLAYED', '2', 'Default number of months displayed in the planning');
INSERT INTO `planning_config` VALUES('DEFAULT_NB_ROWS_DISPLAYED', '100', 'Default number of rows displayed in the planning');
INSERT INTO `planning_config` VALUES('REFRESH_TIMER', '600', 'refresh time for the planning page (time in second)');
INSERT INTO `planning_config` VALUES('LOGOUT_REDIRECT', '', 'Optional redirect url after logout (for exemple to return on your own intranet). ex : http://www.google.com');
INSERT INTO `planning_config` VALUES('DEFAULT_PERIOD_LINK', '', 'Default value for link in a period');
INSERT INTO `planning_config` VALUES('PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY', '0', 'Option to display only one assignment/task per cell/day in the planning (put "1" to activite this option)');
INSERT INTO `planning_config` VALUES('DAYS_INCLUDED', '1,2,3,4,5', 'Define the days included to count duration. IMPORTANT : 0=sunday, 1=monday, 2=tuesday, 3=wenesday, 4=thursday, 5=friday, 6=saturday');
INSERT INTO `planning_config` VALUES('PLANNING_LINE_HEIGHT', '', 'Default line height in the planning. If not specified, it fits the username height');
INSERT INTO `planning_config` VALUES('SOPLANNING_TITLE', 'SOPlanning', 'Change the title of Soplanning for integration in extranet');
INSERT INTO `planning_config` VALUES('SMTP_HOST', 'localhost', '');
INSERT INTO `planning_config` VALUES('SMTP_PORT', '', '');
INSERT INTO `planning_config` VALUES('SMTP_FROM', 'notification@yourdomain.com', '');
INSERT INTO `planning_config` VALUES('SMTP_LOGIN', '', '');
INSERT INTO `planning_config` VALUES('SMTP_PASSWORD', '', '');
INSERT INTO `planning_config` VALUES('SMTP_SECURE', '', '');
INSERT INTO `planning_config` VALUES('SOPLANNING_URL', '', 'Your SOPlanning instance url, to be able to send email with links');
INSERT INTO `planning_config` VALUES('SECURE_KEY', MD5(RAND()), 'String used only for security matters');
INSERT INTO `planning_config` VALUES('PLANNING_REPEAT_HEADER', 0, 'If > 0, repeat header (days/months) in the planning each x lines');
INSERT INTO `planning_config` VALUES('DURATION_AM', '04:00', 'Morning duration when calculating worked hours');
INSERT INTO `planning_config` VALUES('DURATION_PM', '05:00', 'Afternoon duration when calculating worked hours');
INSERT INTO `planning_config` VALUES('DURATION_DAY', '09:00', 'Duration when only one day is selected');
INSERT INTO `planning_config` VALUES('CONTACT_FORM_DEACTIVATE', '', 'Put 1 to deactivate the display of the small button/popin (contact form)');
INSERT INTO `planning_config` VALUES('HOURS_DISPLAYED', '8,9,10,11,14,15,16,17', 'List of hours displayed in the day view');
INSERT INTO `planning_config` VALUES('DEFAULT_NB_DAYS_DISPLAYED', '2', 'Default number of days displayed in the planning view by day');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_LIEUX', '1', 'Location Option');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_RESSOURCES', '1', 'Ressource Option');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_TACHES', '1', 'Task Option');
INSERT INTO `planning_config` VALUES('PLANNING_DATE_FORMAT', '1', 'Date Format');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_ACCES', '0', 'Public access');
INSERT INTO `planning_config` VALUES('SOPLANNING_LOGO', '', 'Logo');
INSERT INTO `planning_config` VALUES('SOPLANNING_THEME', 'soplanning.css', 'Default theme');
INSERT INTO `planning_config` VALUES('PLANNING_COL_WIDTH', '25', 'Planning col width');
INSERT INTO `planning_config` VALUES('PLANNING_COL_WIDTH_LARGE', '130', 'Planning col width large mode');
INSERT INTO `planning_config` VALUES('PLANNING_CODE_WIDTH', '5', 'Code width');
INSERT INTO `planning_config` VALUES('PLANNING_CODE_WIDTH_LARGE', '5', 'Code width large mode');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_VISITEUR', '0', 'Visitor can add or update task');
INSERT INTO `planning_config` VALUES('PLANNING_HIDE_WEEKEND_TASK', '0', 'Hide weekend task');
INSERT INTO `planning_config` VALUES('PLANNING_AFFICHAGE_STATUS', 'aucun', 'Show status');
INSERT INTO `planning_config` VALUES('TIMEZONE', 'Europe/Paris', 'Timezone');
INSERT INTO `planning_config` VALUES('PLANNING_DIFFERENCIE_WEEKEND', '1', 'Week-end class activate');
INSERT INTO `planning_config` VALUES('PLANNING_CELL_FONTSIZE', '0', 'Cell Font size');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT', '1', 'Audit module');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_TACHES', '1', 'Audit tasks');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_PROJETS', '1', 'Audit project');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_GROUPES', '1', 'Audit project group');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_UTILISATEURS', '1', 'Audit users');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_EQUIPES', '1', 'Audit team');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_LIEUX', '1', 'Audit location');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_RESSOURCES', '1', 'Audit ressource');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_STATUTS', '1', 'Audit status');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_CONNEXIONS', '1', 'Audit connexion');
INSERT INTO `planning_config` VALUES('SOPLANNING_OPTION_AUDIT_RETENTION', '30', 'Audit retention');
INSERT INTO `planning_config` VALUES('PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE', '0', 'Task comment');
INSERT INTO `planning_config` VALUES('PLANNING_DIFFERENCIE_TACHE_LIEN', '1', 'Task link');
INSERT INTO `planning_config` VALUES('PLANNING_DIFFERENCIE_TACHE_PARTIELLE', '1', 'Half Task ');
INSERT INTO `planning_config` VALUES('PLANNING_COULEUR_TACHE', '0', 'Task Color');
INSERT INTO `planning_config` VALUES('PLANNING_TEXTE_TACHES_PROJET', 'code_personne', 'Cell text project');
INSERT INTO `planning_config` VALUES('PLANNING_TEXTE_TACHES_PERSONNE', 'code_projet', 'Cell text user');
INSERT INTO `planning_config` VALUES('PLANNING_TEXTE_TACHES_LIEU', 'code_projet', 'Cell text location');
INSERT INTO `planning_config` VALUES('PLANNING_TEXTE_TACHES_RESSOURCE', 'code_projet', 'Cell text resource');
INSERT INTO `planning_config` VALUES('PLANNING_MASQUER_FERIES', '0', 'Hide holidays');
INSERT INTO `planning_config` VALUES('PLANNING_DUREE_CRENEAU_HORAIRE', '30', 'Time duration');
INSERT INTO `planning_config` VALUES('SOPLANNING_API_KEY_NAME', 'SOPLANNING-API', '');
INSERT INTO `planning_config` VALUES('SOPLANNING_API_KEY_VALUE', UUID(),'');
INSERT INTO `planning_config` VALUES('GOOGLE_OAUTH_CLIENT_ID', '', '');
INSERT INTO `planning_config` VALUES('GOOGLE_OAUTH_CLIENT_SECRET', '', '');
INSERT INTO `planning_config` VALUES('GOOGLE_OAUTH_ACTIVE', '0', '');
INSERT INTO `planning_config` VALUES('GOOGLE_2FA_ACTIVE', '0', '');
INSERT INTO `planning_config` VALUES('SEMAPHORE_ACTIVATED', '0', 'Activated in order to avoid periode_id crossing when creating a lot of tasks at the same time');

CREATE TABLE `planning_ferie` (
  `date_ferie` date NOT NULL,
  `libelle` varchar(50) collate latin1_general_ci default NULL,
  `couleur` varchar( 6 ) default NULL,
  PRIMARY KEY  (`date_ferie`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `planning_groupe` (
  `groupe_id` int(11) NOT NULL,
  `nom` varchar(30) collate latin1_general_ci NOT NULL,
  `ordre` int(11) default NULL,
  PRIMARY KEY  (`groupe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `planning_projet` (
  `projet_id` varchar(20) collate latin1_general_ci NOT NULL default '',
  `nom` varchar(50) collate latin1_general_ci NOT NULL default '',
  `iteration` varchar(255) collate latin1_general_ci default NULL,
  `couleur` varchar(6) collate latin1_general_ci NOT NULL default '',
  `charge` float default NULL,
  `livraison` DATE NULL DEFAULT NULL,
  `lien` text collate latin1_general_ci default NULL,
  `statut` varchar(10) NOT NULL collate latin1_general_ci NOT NULL default 'a_faire',
  `groupe_id` int(11) default NULL,
  `createur_id` varchar(20) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`projet_id`),
  KEY `groupe_id` (`groupe_id`),
  CONSTRAINT `planning_projet_ibfk_1` FOREIGN KEY (`groupe_id`) REFERENCES `planning_groupe` (`groupe_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE IF NOT EXISTS `planning_user_groupe` (
  `user_groupe_id` int(11) NOT NULL,
  `nom` varchar(150) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`user_groupe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `planning_user` (
  `user_id` varchar(20) collate latin1_general_ci NOT NULL default '',
  `user_groupe_id` int(11) NULL,
  `nom` varchar(50) collate latin1_general_ci NOT NULL default '',
  `login` varchar(30) collate latin1_general_ci default NULL,
  `password` varchar(50) collate latin1_general_ci default NULL,
  `email` varchar(255) collate latin1_general_ci default NULL,
  `visible_planning` enum('oui','non') collate latin1_general_ci NOT NULL default 'oui',
  `couleur` VARCHAR( 6 ) NULL,
  `droits` text default NULL,
  `cle` VARCHAR(40) NOT NULL default '',
  `notifications` enum('oui','non') collate latin1_general_ci NOT NULL default 'non',
  `adresse` varchar(255) collate latin1_general_ci default NULL,
  `telephone` varchar(20) collate latin1_general_ci default NULL,
  `mobile` varchar(20) collate latin1_general_ci default NULL,
  `metier` varchar(50) collate latin1_general_ci default NULL,
  `commentaire` varchar(255) collate latin1_general_ci default NULL,
  `date_dernier_login` DATETIME NULL,
  `preferences` text default NULL,
  `login_actif` ENUM('oui','non') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'oui',
  `google_2fa` enum('setup','ok') NOT NULL DEFAULT 'setup',
  `date_creation` datetime DEFAULT NULL,
  `date_modif` datetime DEFAULT NULL,
  `tutoriel` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `user_groupe_id` (`user_groupe_id`),
  CONSTRAINT `planning_user_ibfk_1` FOREIGN KEY (`user_groupe_id`) REFERENCES `planning_user_groupe` (`user_groupe_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `planning_periode` (
  `periode_id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NULL,
  `projet_id` varchar(20) collate latin1_general_ci NOT NULL default '',
  `user_id` varchar(20) collate latin1_general_ci NOT NULL default '',
  `link_id` VARCHAR(25) NULL DEFAULT NULL,  
  `date_debut` date NOT NULL default '2000-01-01',
  `date_fin` date default NULL,
  `duree` time default NULL,
  `duree_details` varchar(20) collate latin1_general_ci default NULL,
  `titre` varchar(255) collate latin1_general_ci default NULL,
  `notes` text default NULL,
  `lien` text default NULL,
  `statut_tache` varchar(10) NOT NULL collate latin1_general_ci NOT NULL default 'a_faire',
  `livrable` enum('oui','non') collate latin1_general_ci NOT NULL default 'non',
  `lieu_id` VARCHAR(20) NULL COLLATE 'latin1_general_ci' default NULL,
  `ressource_id` VARCHAR(20) NULL COLLATE 'latin1_general_ci' default NULL,
  `fichiers` TEXT default NULL,
  `createur_id` varchar(20) collate latin1_general_ci NOT NULL,
  `date_creation` DATETIME NULL,
  `modifier_id` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_general_ci NULL,
  `date_modif` DATETIME NULL,
  `custom` varchar(255) collate latin1_general_ci DEFAULT NULL,
  PRIMARY KEY  (`periode_id`),
  KEY `projet_id` (`projet_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `planning_periode_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `planning_projet` (`projet_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `planning_periode_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `planning_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


CREATE TABLE `planning_lieu` (
  `lieu_id` varchar(20) collate latin1_general_ci NOT NULL default '',
  `nom` varchar(50) collate latin1_general_ci NOT NULL default '',
  `commentaire` TEXT collate latin1_general_ci default NULL,
  `exclusif` TINYINT NULL DEFAULT '0' COLLATE 'latin1_general_ci',
  PRIMARY KEY  (`lieu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `planning_ressource` (
  `ressource_id` varchar(20) collate latin1_general_ci NOT NULL default '',
  `nom` varchar(30) collate latin1_general_ci NOT NULL default '',
  `commentaire` TEXT collate latin1_general_ci default NULL,
  `exclusif` TINYINT NULL DEFAULT '0' COLLATE 'latin1_general_ci',
  PRIMARY KEY  (`ressource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


CREATE TABLE `planning_right_on_user` (
  `right_id` int(11) NOT NULL,
  `owner_id` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `allowed_id` varchar(20) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

ALTER TABLE `planning_right_on_user` ADD PRIMARY KEY(`right_id`);

ALTER TABLE `planning_right_on_user`
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `allowed_id` (`allowed_id`);

ALTER TABLE `planning_right_on_user`
  ADD CONSTRAINT `fk_rou_allowed_id` FOREIGN KEY (`allowed_id`) REFERENCES `planning_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rou_owner_id` FOREIGN KEY (`owner_id`) REFERENCES `planning_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

  CREATE TABLE `planning_status` (
    `status_id` varchar(10) collate latin1_general_ci NOT NULL default '',
    `nom` varchar(30) collate latin1_general_ci NOT NULL default '',
    `commentaire` TEXT collate latin1_general_ci default NULL,
    `affichage` VARCHAR(2) NULL DEFAULT 'tp',
    `barre` VARCHAR(2) NULL DEFAULT '0',
    `gras` VARCHAR(2) NULL DEFAULT '0',
    `italique` VARCHAR(2) NULL DEFAULT '0',
    `souligne` VARCHAR(2) NULL DEFAULT '0',
    `defaut` VARCHAR(2) NULL DEFAULT '1',
    `affichage_liste` VARCHAR(2) NULL DEFAULT '1',
    `pourcentage` TINYINT NOT NULL DEFAULT '0' COLLATE 'latin1_general_ci',
    `couleur` VARCHAR( 6 ) NULL,
    `priorite` TINYINT NOT NULL DEFAULT '5' COLLATE 'latin1_general_ci',
    PRIMARY KEY  (`status_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


CREATE TABLE `planning_audit` (
	`audit_id` INT(11) NOT NULL AUTO_INCREMENT,
	`date_modif` DATETIME NOT NULL,
	`user_modif` VARCHAR(20) NOT NULL DEFAULT '' COLLATE 'latin1_general_ci',
	`type` VARCHAR(2) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`user_id` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`projet_id` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`periode_id` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`lieu_id` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`ressource_id` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`statut_id` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`equipe_id` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`groupe_id` VARCHAR(20) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`anciennes_valeurs` TEXT NULL COLLATE 'latin1_general_ci',
	`nouvelles_valeurs` TEXT NULL COLLATE 'latin1_general_ci',
	`nbmodifs` INT(11) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
	`informations` TEXT NULL COLLATE 'latin1_general_ci',
	PRIMARY KEY (`audit_id`),
	INDEX `user_id` (`user_id`),
	INDEX `projet_id` (`projet_id`)
)COLLATE='latin1_general_ci' ENGINE=InnoDB;


