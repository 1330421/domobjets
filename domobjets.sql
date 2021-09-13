########################
# Suppression de table #
########################

DROP TABLE IF EXISTS Products;
DROP TABLE IF EXISTS Brands;
DROP TABLE IF EXISTS Categories;

#####################
# Création de table #
#####################

#
# Table Categories
#

CREATE TABLE IF NOT EXISTS Categories
( idCategory INT AUTO_INCREMENT PRIMARY KEY
, category VARCHAR(50) NOT NULL
);

# Clé unique

ALTER TABLE Categories
ADD CONSTRAINT Categories_category_UK
UNIQUE (category);

#
# Table Brands
#

CREATE TABLE IF NOT EXISTS Brands
( idBrand INT AUTO_INCREMENT PRIMARY KEY
, brand VARCHAR(50) NOT NULL
);

# Clé unique

ALTER TABLE Brands
ADD CONSTRAINT Brands_brand_UK
UNIQUE (brand);

#
# Table Products
#

CREATE TABLE IF NOT EXISTS Products
( idProduct INT AUTO_INCREMENT PRIMARY KEY
, idCategory INT NOT NULL
, idBrand INT NOT NULL
, name VARCHAR(50) NOT NULL
, description VARCHAR(150) NOT NULL
, price FLOAT NOT NULL
, stock_qty INT NOT NULL
, min_threshold_qty INT NOT NULL
, source VARCHAR(250) NOT NULL
);

# Clé unique

ALTER TABLE Products
ADD CONSTRAINT Products_name_UK
UNIQUE (name);

# Clés étrangères

ALTER TABLE Products
ADD CONSTRAINT Products_Categories_FK
FOREIGN KEY (idCategory) REFERENCES Categories(idCategory);

ALTER TABLE Products
ADD CONSTRAINT Products_Brands_FK
FOREIGN KEY (idBrand) REFERENCES Brands(idBrand);

##############
# Insertions #
##############


#
# Insertions de catégories
#

INSERT INTO Categories
( category )
VALUES
( 'Éclairages'
)
,
( 'Caméras'
)
,
( 'Thermostats'
)
,
( 'Hauts-parleurs'
)
,
( 'Interrupteurs'
);

#
# Insertions de marques
#

INSERT INTO Brands
( brand )
VALUES
( 'Philips'
)
,
( 'HeimVision'
)
,
( 'Wyze Labs'
)
,
( 'YI'
)
,
( 'GoControl'
)
,
('Amazon'
)
,
('Nest'
)
,
('Insteon'
)
,
('Homcasito'
)
,
('Bose'
)
,
('Eufy'
)
,
('D-Link'
);

#
# Insertions de produits
#

#1
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Éclairages')
, (SELECT idBrand FROM Brands WHERE brand = 'Philips')
, 'Philips Hue blanche 4 x A19'
, 'Lot de 4 ampoules intelligentes A19 blanches'
, 64.99
, 56
, 10
, 'https://www.amazon.ca/-/fr/ampoules-intelligentes-compatibles-Bluetooth-activ%C3%A9es/dp/B07RVNN9NW/ref=lp_19309426011_1_1?s=specialty-aps'
);

#2
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Éclairages')
, (SELECT idBrand FROM Brands WHERE brand = 'Philips')
, 'Philips Hue blanche 2 x flamme'
, 'Lot de 2 ampoules flamme décorative intelligents blanches'
, 39.99
, 42
, 15
, 'https://www.amazon.ca/-/fr/ampoules-intelligentes-Bluetooth-activation-certifi%C3%A9/dp/B07RSMGSFW/ref=lp_19309426011_1_2?s=specialty-aps'
);

#3
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'HeimVision')
, 'Caméra WiFi intelligente'
, 'Caméra de décurité HM203, 1080p, caméra de surveillance WiFi avec vision nocturne/PTZ/audio bidirectionnel, 2,4 GHz'
, 39.99
, 52
, 10
, 'https://www.amazon.ca/-/fr/HeimVision-surveillance-bidirectionnel-domestiques-personnes/dp/B07PX1YXVM/ref=lp_19309423011_1_1?s=specialty-aps'
);

#4
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'Wyze Labs')
, 'Wyze Cam'
, 'Wyze Cam 1080p HD Caméra d\'intérieur sans fil Smart Home avec vision nocturne, audio bidirectionnel'
, 75.18 
, 22
, 10
, 'https://www.amazon.ca/-/fr/Cam%C3%A9ra-dint%C3%A9rieur-nocturne-bidirectionnel-fonctionne/dp/B07G2YR23M/ref=lp_19309423011_1_2?s=specialty-aps'
);

#5
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'YI')
, 'Caméra de sécurité extérieure'
, 'Caméra de sécurité extérieure étanche 1080p 2,4 GHz Wifi Système de surveillance avec alerte d\'activité, alarme dissuasive'
, 46.39
, 36
, 15
, 'https://www.amazon.ca/-/fr/s%C3%A9curit%C3%A9-ext%C3%A9rieure-surveillance-dactivit%C3%A9-dissuasive/dp/B01CW49AGG/ref=lp_19309423011_1_6?s=specialty-aps'
);

#6
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'YI')
, 'Caméra de sécurité dôme'
, 'Caméra de sécurité dôme 1080p, PTZ 2,4 G WiFi Système de surveillance, alerte de détection de mouvement, croisière automatique'
, 39.99
, 48
, 15
, 'https://www.amazon.ca/-/fr/surveillance-streaming-d%C3%A9tection-automatique-application/dp/B01CW4BLG8/ref=lp_19309423011_1_7?s=specialty-aps'
);

#7
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Thermostats')
, (SELECT idBrand FROM Brands WHERE brand = 'GoControl')
, 'Z-Wave Thermostat intelligent'
, 'Z-Wave Thermostat intelligent à piles'
, 161.89
, 26
, 10
, 'https://www.amazon.ca/-/fr/Z-Wave-Thermostat-intelligent-%C3%A0-piles/dp/B00ZIRV40K/ref=lp_19309434011_1_5?s=specialty-aps'
);

#8
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Hauts-parleurs')
, (SELECT idBrand FROM Brands WHERE brand = 'Amazon')
, 'Echo Studio'
, 'Voici Echo Studio - Son immersif haute-fidélité et Alexa intégré'
, 259.99
, 14
, 10
, 'https://www.amazon.ca/-/fr/Voici-Echo-Studio-immersif-haute-fid%C3%A9lit%C3%A9/dp/B07NQDP34D/ref=lp_19309432011_1_7?s=specialty-aps'
);

#9
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Hauts-parleurs')
, (SELECT idBrand FROM Brands WHERE brand = 'Amazon')
, 'Echo Flex'
, 'Voici Echo Flex - Appareil intelligent avec haut-parleur et Alexa intégré'
, 34.99
, 15
, 33
, 'https://www.amazon.ca/-/fr/Voici-Echo-Flex-intelligent-haut-parleur/dp/B07PJ1ZDTX/ref=lp_19309432011_1_9?s=specialty-aps'
);

#10
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Hauts-parleurs')
, (SELECT idBrand FROM Brands WHERE brand = 'Amazon')
, 'Echo Show 8'
, 'Son de qualité supérieure et écran intelligent HD 8 po avec Alexa intégré'
, 99.99
, 38
, 10
, 'https://www.amazon.ca/-/fr/Voici-Echo-Show-sup%C3%A9rieure-intelligent/dp/B07SH9J659/ref=lp_19309432011_1_6?s=specialty-aps&th=1'
);

#11
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Hauts-parleurs')
, (SELECT idBrand FROM Brands WHERE brand = 'Amazon')
, 'Echo Show 5'
, 'Écran intelligent et compact avec Alexa'
, 64.99
, 18
, 15
, 'https://www.amazon.ca/-/fr/Echo-Show-intelligent-connect%C3%A9-Anthracite/dp/B07KD6RCKS/ref=lp_19309432011_1_5?s=specialty-aps'
);

#12
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'YI')
, 'Caméra à domicile'
, 'Système de surveillance de sécurité avec vision nocturne pour moniteur de bureau, 1080p sans fil'
, 26.39
, 15
, 29
, 'https://www.amazon.ca/-/fr/domicile-surveillance-s%C3%A9curit%C3%A9-nocturne-moniteur/dp/B01CW4AR9K/ref=lp_19309423011_1_3?s=specialty-aps'
);

#13
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'Nest')
, 'Caméra de sécurité'
, 'Caméra de sécurité avec vision nocturne, 1080p'
, 169.03
, 37
, 10
, 'https://www.amazon.ca/-/fr/NC1102EF-Nest-Cam%C3%A9ra-de-s%C3%A9curit%C3%A9/dp/B00XJ59Q4E/ref=lp_19309423011_1_8?s=specialty-aps'
);

#14
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Thermostats')
, (SELECT idBrand FROM Brands WHERE brand = 'Insteon')
, 'Thermostat '
, 'Économiser de l\'argent sur les factures d\'énergie en contrôlant à distance et programmation de votre thermostat'
, 105.28
, 10
, 21
, 'https://www.amazon.ca/-/fr/2441TH-INSTEON-Thermostat-RF-uniquement/dp/B007X5TY16/ref=lp_19309434011_1_6?s=specialty-aps'
);

#15
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Éclairages')
, (SELECT idBrand FROM Brands WHERE brand = 'Homcasito')
, 'Veilleuses Bluetooth'
, 'Veilleuses Bluetooth haut-parleur tactile, lampe de chevet à capteur tactile, veilleuse RVB à intensité variable'
, 45.99
, 54
, 15
, 'https://www.amazon.ca/-/fr/Homcasito-Veilleuses-Bluetooth-haut-parleur-calendrier/dp/B07H83JCB7/ref=lp_19309426011_1_3?s=specialty-aps'
);

#16
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Hauts-parleurs')
, (SELECT idBrand FROM Brands WHERE brand = 'Bose')
, 'Enceinte Home 500'
, 'Enceinte Home 500 avec commande vocale Alexa intégrée'
, 399.00
, 43
, 10
, 'https://www.amazon.ca/-/fr/Enceinte-commande-vocale-Alexa-int%C3%A9gr%C3%A9e/dp/B07FDF9B46/ref=sr_1_17?dchild=1&qid=1614288909&s=smart-home&sr=1-17'
);

#17
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Hauts-parleurs')
, (SELECT idBrand FROM Brands WHERE brand = 'Amazon')
, 'Echo Sub'
, 'Caisson de basses puissant pour votre Echo. Nécessite un appareil Echo compatible'
, 169.99
, 36
, 10
, 'https://www.amazon.ca/-/fr/Caisson-puissant-N%C3%A9cessite-appareil-compatible/dp/B07F3PTF7X/ref=sr_1_20?dchild=1&qid=1614289055&s=smart-home&sr=1-20'
);

#18
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'Nest')
, 'Caméra de surveillance extérieure'
, 'Caméra de surveillance extérieure, grand angle de vue de 130°, 1080p'
, 249.99
, 12
, 10
, 'https://www.amazon.ca/-/fr/Cam%C3%A9ra-surveillance-ext%C3%A9rieure-fonctionne-Amazon/dp/B01IOW8FQQ/ref=sr_1_16?dchild=1&qid=1614289238&sr=8-16'
);

#19
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'Eufy')
, 'Caméra de sécurité sans fil'
, 'Caméra de sécurité sans fil, 365 jours d\'autonomie, 1080p HD, audio bidirectionnel, résistant aux intempéries'
, 159.99
, 54
, 10
, 'https://www.amazon.ca/-/fr/dautonomie-bidirectionnel-intemp%C3%A9ries-utilisation-suppl%C3%A9mentaire/dp/B07GJMD6MB/ref=sr_1_18?dchild=1&qid=1614289376&sr=8-18'
);

#20
INSERT INTO Products
( idCategory, idBrand, name, description, price, stock_qty, min_threshold_qty, source )
VALUES
( (SELECT idCategory FROM Categories WHERE category = 'Caméras')
, (SELECT idBrand FROM Brands WHERE brand = 'D-Link')
, 'Caméra de sécurité WiFi HD'
, 'Caméra de sécurité WiFi HD Mini Intérieure, Enregistrement Cloud, Détection de mouvement et Vision de nuit'
, 54.87
, 24
, 15
, 'https://www.amazon.ca/-/fr/Int%C3%A9rieure-Enregistrement-D%C3%A9tection-Fonctionne-DCS-8000LH/dp/B07PH3PDNZ/ref=sr_1_19?dchild=1&qid=1614289376&sr=8-19'
);