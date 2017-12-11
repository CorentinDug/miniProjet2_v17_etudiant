DROP TABLE  IF EXISTS paniers,commentaires,commandes, produits, users, typeProduits, etats;

-- --------------------------------------------------------
-- Structure de la table typeproduits
--
CREATE TABLE IF NOT EXISTS typeProduits (
  id int(10) NOT NULL,
  libelle varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;
-- Contenu de la table typeproduits
INSERT INTO typeProduits (id, libelle) VALUES
(1, 'Sportive Classique'),
(2, 'Muscle Car'),
(3, 'Sportive');

-- --------------------------------------------------------
-- Structure de la table etats

CREATE TABLE IF NOT EXISTS etats (
  id int(11) NOT NULL AUTO_INCREMENT,
  libelle varchar(20) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 ;
-- Contenu de la table etats
INSERT INTO etats (id, libelle) VALUES
(1, 'A préparer'),
(2, 'Expédié'),
(3, 'Livré');

-- --------------------------------------------------------
-- Structure de la table produits

CREATE TABLE IF NOT EXISTS produits (
  id int(10) NOT NULL AUTO_INCREMENT,
  typeProduit_id int(10) DEFAULT NULL,
  nom varchar(50) DEFAULT NULL,
  prix float(15,2) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  dispo tinyint(4) NOT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_produits_typeProduits FOREIGN KEY (typeProduit_id) REFERENCES typeProduits (id)
) DEFAULT CHARSET=utf8 ;

INSERT INTO produits (id,typeProduit_id,nom,prix,photo,dispo,stock) VALUES
(1,3, 'Ferrari 458 Speciale','100000','458speciale.jpeg',1,5),
(2,3, 'McLaren 720S','200000','720s.jpeg',1,4),
(3,1, 'Porsche 911 Turbo','50000','911.jpeg',1,4),
(4,2, 'Chevrolet Camaro SS','35000','camaro.jpeg',1,5),
(5,2, 'Dodge Charger','20000','charger.jpeg',1,4),
(6,3, 'Aston Martin DB11','250000','db11.jpeg',1,10),
(7,1, 'Ferrari F40','1000000','f40.jpeg',1,2),
(8,3, 'Jaguar F-Type SVR','190000','fTypeSVR.jpeg',1,6),
(9,3, 'Lamborghini Murcielago','350000','murcielago.jpeg',1,3),
(10,2, 'Ford Mustang','65000','mustang.jpeg',1,4),
(11,3, 'Audi R8 V10','75000','r8v10.jpeg',1,3),
(12,1, 'Ferrari TestaRossa','115000','testaRossa.jpeg',1,7);


-- --------------------------------------------------------
-- Structure de la table user
-- valide permet de rendre actif le compte (exemple controle par email )


# Structure de la table `utilisateur`
DROP TABLE IF EXISTS users;

# <http://silex.sensiolabs.org/doc/2.0/providers/security.html#defining-a-custom-user-provider>
# Contenu de la table `utilisateur`

CREATE TABLE users (

  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL DEFAULT '',
  motdepasse VARCHAR(255) NOT NULL DEFAULT '',
  roles VARCHAR(255) NOT NULL DEFAULT 'ROLE_CLIENT',
  email  VARCHAR(255) NOT NULL DEFAULT '',
  isEnabled TINYINT(1) NOT NULL DEFAULT 1,

  nom varchar(255),
  code_postal varchar(255),
  ville varchar(255),
  adresse varchar(255),

  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# mot de passe crypté avec security.encoder.bcrypt

INSERT INTO users (id,username,password,motdepasse,email,roles) VALUES
(1, 'admin', 'd05cc09587a5589671f59966bea4fb12', 'admin', 'admin@gmail.com','ROLE_ADMIN'),
(2, 'invite', '96260fb4892518eed8ae345e5fe886e7', 'invite', 'admin@gmail.com','ROLE_INVITE'),
(3, 'vendeur', '97049651da4aa9acf7f2ec0b62af3c52','vendeur', 'vendeur@gmail.com','ROLE_VENDEUR'),
(4, 'client', '2f9dab7127378d55a4121d855266074c', 'client', 'client@gmail.com','ROLE_CLIENT'),
(5, 'client2', '2b49abae6e13396373d67063c6473efb','client2', 'client2@gmail.com','ROLE_CLIENT');



-- --------------------------------------------------------
-- Structure de la table commandes
CREATE TABLE IF NOT EXISTS commandes (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11)  UNSIGNED  NOT NULL,
  prix float(20,2) NOT NULL,
  date_achat  timestamp default CURRENT_TIMESTAMP,
  etat_id int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_commandes_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_commandes_etats FOREIGN KEY (etat_id) REFERENCES etats (id)
) DEFAULT CHARSET=utf8 ;



-- --------------------------------------------------------
-- Structure de la table paniers
CREATE TABLE IF NOT EXISTS paniers (
  id int(11) NOT NULL AUTO_INCREMENT,
  quantite int(11) NOT NULL,
  prix float(20,2) NOT NULL,
  dateAjoutPanier timestamp default CURRENT_TIMESTAMP,
  user_id int(11)  UNSIGNED  NOT NULL,
  produit_id int(11) NOT NULL,
  commande_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_paniers_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_paniers_produits FOREIGN KEY (produit_id) REFERENCES produits (id),
  CONSTRAINT fk_paniers_commandes FOREIGN KEY (commande_id) REFERENCES commandes (id)
) DEFAULT CHARSET=utf8 ;

-- ----------------------------------------------------------
-- Structure de la table commentaires
CREATE TABLE IF NOT EXISTS commentaires(
  id int(11) NOT NULL AUTO_INCREMENT,
  commentaire varchar(1000) NOT NULL,
  produit_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  PRIMARY KEY(id),
  CONSTRAINT fk_commentaires_produits FOREIGN KEY (produit_id) REFERENCES produits (id),
  CONSTRAINT fk_commentaires_users FOREIGN KEY (user_id) REFERENCES users (id)
)DEFAULT CHARSET=utf8;
