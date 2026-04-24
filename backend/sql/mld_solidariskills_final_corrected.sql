  
-- ============================================
-- Script Solidariskills
-- ============================================

-- Création de la table admin
CREATE TABLE admin (
    id_admin SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    mdp VARCHAR(255) NOT NULL
);

-- Création de la table utilisateur (corrigée)
CREATE TABLE utilisateur (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,

    nom VARCHAR(100) NOT NULL,
    num_rue INT CHECK (num_rue BETWEEN 1 AND 99),
    adresse VARCHAR(255),
    code_postal INT CHECK (code_postal BETWEEN 0 AND 99999),
    ville VARCHAR(100),
    date_inscription DATE DEFAULT CURRENT_DATE,
    statut VARCHAR(10) NOT NULL CHECK (statut IN ('Admin', 'Membre')),
    nb_points INT DEFAULT 0,
    id_admin_gerant INT,
    description TEXT,
    imagePDP VARCHAR(255),
    
    FOREIGN KEY (id_admin_gerant) REFERENCES admin(id_admin)
);

-- Météo
CREATE TABLE meteo (
    loc_meteo VARCHAR(100),
    date_meteo DATE,
    condition_meteo VARCHAR(20) CHECK (condition_meteo IN ('Sec', 'Pluie', 'Neige')),
    temperature_meteo INT CHECK (temperature_meteo BETWEEN -50 AND 50),
    PRIMARY KEY (loc_meteo, date_meteo)
);

-- Activité
CREATE TABLE activite (
    id_act SERIAL PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    localisation VARCHAR(100) NOT NULL,
    nb_places INT NOT NULL CHECK (nb_places >= 1),
    conditions_req VARCHAR(20) CHECK (conditions_req IN ('Sec', 'Pluie', 'Neige', 'NULL')),
    loc_meteo VARCHAR(100),
    date_meteo DATE,
    id_admin INT,
    date_activite DATE NOT NULL,
    FOREIGN KEY (loc_meteo, date_meteo) REFERENCES meteo(loc_meteo, date_meteo),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin)
);

-- Compétence
CREATE TABLE competence (
    id_competence SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    categorie VARCHAR(50),
    niveau VARCHAR(20) CHECK (niveau IN ('débutant', 'intermédiaire', 'expert')),
    id_admin_gerant INT,
    FOREIGN KEY (id_admin_gerant) REFERENCES admin(id_admin)
);

-- Badge
CREATE TABLE badge (
    id_badge SERIAL PRIMARY KEY,
    nom_badge VARCHAR(50) NOT NULL,
    description_badge VARCHAR(255)
);

-- Feedback
CREATE TABLE feedback (
    id_fb SERIAL PRIMARY KEY,
    titre_fb VARCHAR(100) NOT NULL,
    note_fb INT CHECK (note_fb BETWEEN 1 AND 5) NOT NULL,
    commentaire_fb TEXT,
    date_fb DATE NOT NULL,
    id_user INT NOT NULL,
    id_act INT NOT NULL,
    id_admin_gerant INT,
    FOREIGN KEY (id_user) REFERENCES utilisateur(id),
    FOREIGN KEY (id_act) REFERENCES activite(id_act),
    FOREIGN KEY (id_admin_gerant) REFERENCES admin(id_admin)
);

-- Message
CREATE TABLE message (
    id_msg SERIAL PRIMARY KEY,
    contenu_msg VARCHAR(255) NOT NULL,
    date_msg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_expediteur INT NOT NULL REFERENCES utilisateur(id),
    id_destinataire INT NOT NULL REFERENCES utilisateur(id)
);

-- Calendrier
CREATE TABLE calendrier (
    id_calendrier SERIAL PRIMARY KEY,
    date_calendrier TIMESTAMP NOT NULL,
    type_evenement VARCHAR(20) CHECK (type_evenement IN ('Activité', 'Disponibilité', 'Rappel')),
    id_act INT REFERENCES activite(id_act),
    id_user INT REFERENCES utilisateur(id)
);

-- Associations n,n
CREATE TABLE attribuer (
    id INT NOT NULL,
    id_badge INT NOT NULL,
    PRIMARY KEY (id, id_badge),
    FOREIGN KEY (id) REFERENCES utilisateur(id),
    FOREIGN KEY (id_badge) REFERENCES badge(id_badge)
);

CREATE TABLE reserver (
    id INT NOT NULL,
    id_act INT NOT NULL,
    date_reservation DATE NOT NULL,
    PRIMARY KEY (id, id_act),
    FOREIGN KEY (id) REFERENCES utilisateur(id),
    FOREIGN KEY (id_act) REFERENCES activite(id_act)
);

CREATE TABLE gerer_cpu (
    id INT NOT NULL,
    id_competence INT NOT NULL,
    PRIMARY KEY (id, id_competence),
    FOREIGN KEY (id) REFERENCES utilisateur(id),
    FOREIGN KEY (id_competence) REFERENCES competence(id_competence)
);

CREATE TABLE participer (
    id INT NOT NULL,
    id_act INT NOT NULL,
    PRIMARY KEY (id, id_act),
    FOREIGN KEY (id) REFERENCES utilisateur(id),
    FOREIGN KEY (id_act) REFERENCES activite(id_act)
);

CREATE TABLE necessite (
    id_act INT NOT NULL,
    id_competence INT NOT NULL,
    PRIMARY KEY (id_act, id_competence),
    FOREIGN KEY (id_act) REFERENCES activite(id_act),
    FOREIGN KEY (id_competence) REFERENCES competence(id_competence)
);

CREATE TABLE ajouter (
    id_user_source INT NOT NULL,
    id_user_cible INT NOT NULL,
    date_ajt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'en_attente' CHECK (statut IN ('en_attente', 'accepté', 'refusé')),
    PRIMARY KEY (id_user_source, id_user_cible),
    FOREIGN KEY (id_user_source) REFERENCES utilisateur(id),
    FOREIGN KEY (id_user_cible) REFERENCES utilisateur(id),
    CONSTRAINT no_self_contact CHECK (id_user_source <> id_user_cible)
);

-- Association ternaire
CREATE TABLE ajouterd (
    id_admin INT NOT NULL,
    id INT NOT NULL,
    date_cr DATE NOT NULL,
    heure_debut TIME NOT NULL,
    PRIMARY KEY (id_admin, id, date_cr, heure_debut),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin),
    FOREIGN KEY (id) REFERENCES utilisateur(id)
);














-- 1 activité par thème, avec date_activite, theme et id_createur
INSERT INTO activite (
  titre,
  description,
  localisation,
  nb_places,
  conditions_req,
  loc_meteo,
  date_meteo,
  id_admin,
  date_activite,
  theme,
  id_createur
) VALUES
-- Jardinage
(
  'Découverte du jardin écologique',
  'Initiez-vous aux bases du jardinage durable en pleine nature.',
  'Jardin public',
  15,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-01',
  'jardinage',
  12
),

-- Programmation
(
  'Initiation à Python pour tous',
  'Apprenez les concepts fondamentaux de Python et écrivez vos premiers scripts.',
  'Salle informatique',
  20,
  'Pluie',
  NULL,
  NULL,
  NULL,
  '2025-06-02',
  'programmation',
  12
),

-- Poterie
(
  'Atelier de poterie pour débutants',
  'Modelage, centrage et décoration : réalisez votre première pièce en argile.',
  'Atelier d art local',
  10,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-03',
  'poterie',
  12
),

-- Sport
(
  'Initiation au basketball',
  'Maîtrisez les fondamentaux du basketball en équipe et sur grand terrain.',
  'Terrain municipal',
  12,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-04',
  'sport',
  12
),

-- Cuisine
(
  'Atelier cuisine italienne',
  'Réalisez pâtes fraîches et sauce tomate maison dans une ambiance conviviale.',
  'Cuisine communautaire',
  8,
  'Pluie',
  NULL,
  NULL,
  NULL,
  '2025-06-05',
  'cuisine',
  12
),

-- Musique
(
  'Découverte de la guitare acoustique',
  'Apprenez vos premiers accords et jouez une mélodie simple.',
  'Studio de musique',
  10,
  'NULL',
  NULL,
  NULL,
  NULL,
  '2025-06-06',
  'musique',
  12
),

-- Photographie
(
  'Balade photo urbaine',
  'Explorez les techniques de cadrage et de composition en ville.',
  'Centre-ville',
  20,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-07',
  'photo',
  12
),

-- Bricolage
(
  'Réparation de meubles en bois',
  'Apprenez à démonter, réparer et remonter un petit meuble endommagé.',
  'Garage associatif',
  6,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-08',
  'bricolage',
  12
),

-- Lecture
(
  'Club de lecture mensuel',
  'Partagez vos impressions et analyses d’un roman sélectionné.</p>',
  'Bibliothèque municipale',
  25,
  'NULL',
  NULL,
  NULL,
  NULL,
  '2025-06-09',
  'lecture',
  12
),

-- Écriture
(
  'Atelier d’écriture créative',
  'Exercices et conseils pour rédiger votre première courte histoire.',
  'Médiathèque',
  15,
  'NULL',
  NULL,
  NULL,
  NULL,
  '2025-06-10',
  'écriture',
  12
),

-- Ski
(
  'Cours de ski alpin débutant',
  'Initiez-vous aux techniques de base du ski alpin avec un moniteur.',
  'Station des Neiges',
  20,
  'Neige',
  NULL,
  NULL,
  NULL,
  '2025-06-11',
  'ski',
  12
),

-- Autre
(
  'Découverte surprise',
  'Une activité surprise pour explorer une nouvelle compétence.',
  'Lieu à déterminer',
  30,
  'NULL',
  NULL,
  NULL,
  NULL,
  '2025-06-12',
  'Autre',
  12
);


INSERT INTO activite (
  titre,
  description,
  localisation,
  nb_places,
  conditions_req,
  loc_meteo,
  date_meteo,
  id_admin,
  date_activite,
  theme,
  id_createur
) VALUES
-- Jardinage
(
  'Création d’un compost collectif',
  'Apprenez à transformer vos déchets organiques en compost naturel.',
  'Parc municipal',
  12,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-13',
  'jardinage',
  13
),

-- Programmation
(
  'Jeux vidéo en JavaScript',
  'Créez un mini-jeu interactif avec HTML, CSS et JavaScript.',
  'Salle multimédia',
  15,
  'Pluie',
  NULL,
  NULL,
  NULL,
  '2025-06-14',
  'programmation',
  13
),

-- Poterie
(
  'Sculpture sur argile',
  'Apprenez les techniques de base pour sculpter des formes artistiques.',
  'Atelier associatif',
  8,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-15',
  'poterie',
  13
),

-- Sport
(
  'Découverte du yoga en plein air',
  'Relaxation et renforcement musculaire au cœur de la nature.',
  'Parc du centre-ville',
  20,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-16',
  'sport',
  13
),

-- Cuisine
(
  'Recettes végétariennes express',
  'Préparez des plats équilibrés et rapides à base de produits frais.',
  'Salle de cuisine partagée',
  10,
  'Pluie',
  NULL,
  NULL,
  NULL,
  '2025-06-17',
  'cuisine',
  13
),

-- Musique
(
  'Chorale solidaire',
  'Chantez en groupe des morceaux simples et harmonieux.',
  'Salle des fêtes',
  25,
  'NULL',
  NULL,
  NULL,
  NULL,
  '2025-06-18',
  'musique',
  13
),

-- Photographie
(
  'Photo nature et biodiversité',
  'Capturez la faune et la flore locale avec votre appareil.',
  'Forêt domaniale',
  15,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-19',
  'photo',
  13
),

-- Bricolage
(
  'Création d’un nichoir à oiseaux',
  'Fabriquez un abri pour oiseaux avec du bois recyclé.',
  'Atelier communautaire',
  10,
  'Sec',
  NULL,
  NULL,
  NULL,
  '2025-06-20',
  'bricolage',
  13
),

-- Lecture
(
  'Lecture à voix haute intergénérationnelle',
  'Lisez des histoires à des enfants ou personnes âgées.',
  'Maison de quartier',
  20,
  'NULL',
  NULL,
  NULL,
  NULL,
  '2025-06-21',
  'lecture',
  13
),

-- Écriture
(
  'Écrire une lettre à son futur soi',
  'Prenez un moment pour rédiger une lettre personnelle à ouvrir dans 5 ans.',
  'Salle polyvalente',
  12,
  'NULL',
  NULL,
  NULL,
  NULL,
  '2025-06-22',
  'écriture',
  13
),

-- Ski
(
  'Balade en raquettes et initiation',
  'Explorez un sentier enneigé et découvrez la marche en raquettes.',
  'Station des Cimes',
  15,
  'Neige',
  NULL,
  NULL,
  NULL,
  '2025-06-23',
  'ski',
  13
),

-- Autre
(
  'Atelier de sensibilisation écologique',
  'Jeu de rôle et échanges autour de la transition écologique.',
  'Maison des associations',
  25,
  'NULL',
  NULL,
  NULL,
  NULL,
  '2025-06-24',
  'Autre',
  13
);













-- 1) Ajouter la colonne (NULL par défaut)
ALTER TABLE activite
  ADD COLUMN competence_req INT;

-- 2) Créer la contrainte de clé étrangère
ALTER TABLE activite
  ADD CONSTRAINT fk_activite_competence
  FOREIGN KEY (competence_req)
  REFERENCES competence(id_competence);










INSERT INTO competence (nom, categorie, niveau) VALUES
-- Jardinage
('Planter et semer des graines',             'jardinage',      'débutant'),
('Tailler et entretenir des arbustes',        'jardinage',      'débutant'),
('Composer et entretenir un potager',         'jardinage',      'débutant'),
('Reconnaître maladies et ravageurs',         'jardinage',      'débutant'),
('Amender et fertiliser le sol',              'jardinage',      'débutant'),

-- Programmation
('Écrire et commenter une fonction MVC',     'programmation',  'débutant'),
('Interroger et modifier une BDD SQL',        'programmation',  'débutant'),
('Déboguer et tester du code',                'programmation',  'débutant'),
('Consommer une API REST',                    'programmation',  'débutant'),
('Gérer le versioning avec Git',              'programmation',  'débutant'),

-- Poterie
('Modeler l’argile à la main',                'poterie',        'débutant'),
('Utiliser et centrer l’argile sur un tour',  'poterie',        'débutant'),
('Appliquer et maîtriser les émaux',          'poterie',        'débutant'),
('Contrôler les cuissons',                    'poterie',        'débutant'),
('Décorer avec engobes et incision',          'poterie',        'débutant'),

-- Sport
('Réaliser un échauffement efficace',         'sport',          'débutant'),
('Maîtriser techniques de base (passes/tirs)','sport',          'débutant'),
('Gérer endurance et récupération',           'sport',          'débutant'),
('Appliquer règles de sécurité',              'sport',          'débutant'),
('Concevoir plan d’entraînement',             'sport',          'débutant'),

-- Cuisine
('Maîtriser découpe et taillage',             'cuisine',        'débutant'),
('Préparer sauces et vinaigrettes',           'cuisine',        'débutant'),
('Contrôler la cuisson',                      'cuisine',        'débutant'),
('Assaisonner et équilibrer saveurs',         'cuisine',        'débutant'),
('Dresser une assiette professionnelle',      'cuisine',        'débutant'),

-- Musique
('Lire une partition (clé de sol/fa)',        'musique',        'débutant'),
('Tenir le rythme et métronome',              'musique',        'débutant'),
('Jouer un instrument (accords de base)',     'musique',        'débutant'),
('Chanter juste et gérer la respiration',     'musique',        'débutant'),
('Composer une courte mélodie',               'musique',        'débutant'),

-- Photographie
('Composer une image (règle des tiers)',      'photo',          'débutant'),
('Maîtriser l’exposition',                    'photo',          'débutant'),
('Utiliser mise au point manuelle',           'photo',          'débutant'),
('Post-traitement basique (Lightroom)',       'photo',          'débutant'),
('Choisir le bon objectif',                   'photo',          'débutant'),

-- Bricolage
('Utiliser outils courants',                  'bricolage',      'débutant'),
('Mesurer, tracer et découper',               'bricolage',      'débutant'),
('Assembler et fixer',                        'bricolage',      'débutant'),
('Poncer et peindre une finition',            'bricolage',      'débutant'),
('Monter un meuble en kit',                   'bricolage',      'débutant'),

-- Lecture
('Identifier le thème principal',             'lecture',        'débutant'),
('Prendre des notes efficacement',            'lecture',        'débutant'),
('Analyser le style et registre',             'lecture',        'débutant'),
('Résumer un texte de manière concise',       'lecture',        'débutant'),
('Émettre une critique argumentée',           'lecture',        'débutant'),

-- Écriture
('Structurer un texte (plan, chapitres)',     'écriture',       'débutant'),
('Rédiger un titre accrocheur',               'écriture',       'débutant'),
('Employer un vocabulaire précis',            'écriture',       'débutant'),
('Corriger et relire pour éliminer fautes',   'écriture',       'débutant'),
('Adapter le ton au public',                  'écriture',       'débutant'),

-- Ski
('Glisser et freiner en chasse-neige',        'ski',            'débutant'),
('Effectuer des virages parallèles',          'ski',            'débutant'),
('Gérer équilibre et appui',                  'ski',            'débutant'),
('Monter/descendre téléski',                  'ski',            'débutant'),
('Adapter posture en terrain varié',          'ski',            'débutant'),

-- Autre
('Capacité d’adaptation rapide',              'Autre',          'débutant'),
('Esprit critique et résolution de problèmes','Autre',          'débutant'),
('Gestion du temps et des priorités',         'Autre',          'débutant'),
('Communication orale et écrite claire',      'Autre',          'débutant'),
('Travail en équipe et esprit collaboratif',  'Autre',          'débutant');
