-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: moviz
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `director`
--

DROP TABLE IF EXISTS `director`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `director` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `director`
--

LOCK TABLES `director` WRITE;
/*!40000 ALTER TABLE `director` DISABLE KEYS */;
INSERT INTO `director` VALUES (1,'Christopher','Nolan'),(2,'David','Ayer'),(3,'Rob','Savage'),(4,'Zach','Cregger'),(5,'Destin','Daniel Cretton'),(6,'Na','Hong-jin'),(7,'Phil','Lord'),(8,'Christopher','Miller'),(9,'Sidharta','Tata'),(10,'Tazbah','Chavez'),(11,'Mike','P. Nelson'),(12,'Joe','Russo'),(13,'Anthony','Russo'),(14,'Francis','Lawrence'),(15,'Carles','Torrens'),(16,'Mark','Vanselow'),(17,'Ti','West'),(18,'Nitesh','Tiwari'),(19,'Takashi','Yamazaki'),(20,'William','Eubank'),(21,'Javier','Ambrossi'),(22,'Javier','Calvo'),(23,'Shana','Feste'),(24,'tamra','davis'),(25,'Mohamed','Hamidi');
/*!40000 ALTER TABLE `director` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genre` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genre`
--

LOCK TABLES `genre` WRITE;
/*!40000 ALTER TABLE `genre` DISABLE KEYS */;
INSERT INTO `genre` VALUES (1,'Science-Fiction'),(2,'Action'),(3,'Thriller'),(4,'Aventure'),(5,'Fantastique'),(6,'Horreur'),(7,'Mystère'),(8,'Crime'),(9,'Drame'),(10,'Comédie'),(11,'Guerre');
/*!40000 ALTER TABLE `genre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movie`
--

DROP TABLE IF EXISTS `movie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tmdb_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `synopsis` text NOT NULL,
  `release_date` date DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tmdb_id` (`tmdb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movie`
--

LOCK TABLES `movie` WRITE;
/*!40000 ALTER TABLE `movie` DISABLE KEYS */;
INSERT INTO `movie` VALUES (1,NULL,'Inception','Dom Cobb est un voleur expérimenté – le meilleur qui soit dans l’art périlleux de l’extraction : sa spécialité consiste à s’approprier les secrets les plus précieux d’un individu, enfouis au plus profond de son subconscient, pendant qu’il rêve et que son esprit est particulièrement vulnérable. Très recherché pour ses talents dans l’univers trouble de l’espionnage industriel, Cobb est aussi devenu un fugitif traqué dans le monde entier qui a perdu tout ce qui lui est cher. Mais une ultime mission pourrait lui permettre de retrouver sa vie d’avant – à condition qu’il puisse accomplir l’impossible : l’inception. Au lieu de subtiliser un rêve, Cobb et son équipe doivent faire l’inverse : implanter une idée dans l’esprit d’un individu. S’ils y parviennent, il pourrait s’agir du crime parfait. Et pourtant, aussi méthodiques et doués soient-ils, rien n’aurait pu préparer Cobb et ses partenaires à un ennemi redoutable qui semble avoir systématiquement un coup d’avance sur eux. Un ennemi dont seul Cobb aurait pu soupçonner l’existence.','2010-07-21','02:28:00','inception.png'),(2,NULL,'Interstellar','Le film raconte les aventures d’un groupe d’explorateurs qui utilisent une faille récemment découverte dans l’espace-temps afin de repousser les limites humaines et partir à la conquête des distances astronomiques dans un voyage interstellaire.','2014-11-05',NULL,'interstellar.jpg'),(3,1263337,'Heart of the Beast','Après un terrible crash d\'avion, James Belmont, ancien des forces spéciales, se retrouve perdu au cœur de l\'Alaska avec son chien Odin, depuis toujours à ses côtés. Livrés à eux-mêmes dans une nature aussi magnifique qu\'impitoyable, ils devront puiser dans leurs dernières forces pour survivre ensemble.','2026-09-19','01:41:00','tmdb-4t1ASmI2IQEAoO8hirEGQ6nMEib.jpg'),(4,1368337,'L\'Odyssée','Ulysse, roi d\'Ithaque, entreprend un périple périlleux pour regagner son royaume après la guerre de Troie. Alors qu\'ils traversent la Méditerranée avec ses soldats, ils se retrouvent bientôt à lutter non seulement contre les éléments, mais aussi contre une multitude d\'obstacles mortels et de créatures mythiques.','2026-07-15','02:53:00','tmdb-jKf0p3fhO893ROvRuvuSDsZSvK6.jpg'),(5,1400837,'Malfaisante','Une jeune fille, Bela, développe dans son foyer une relation avec une entité troublante qui ressemble étrangement à sa mère. Son « autre maman » n\'est pas que le fruit de son imagination. Cette entité sinistre et manipulatrice a l\'intention d\'anéantir la famille de Bela.','2026-10-07','01:33:00','tmdb-a6AztQlJvKy2tEOc10M7WXIF5gX.jpg'),(6,1423191,'Resident Evil','Bryan, un coursier médical, est en train d\'effectuer une livraison lorsqu\'une épidémie soudaine se déclare, le forçant à lutter pour sa survie.','2026-09-16','01:34:00','tmdb-pICoWjcKSet6sA3yzxOkP8ChwYI.jpg'),(7,969681,'Spider-Man : Brand New Day','Quatre ans se sont écoulés, Peter, désormais adulte, vit seul, s\'est volontairement effacé de la vie et des souvenirs de ses proches. Luttant contre le crime dans un New York qui ne le reconnaît plus, il se consacre entièrement à la protection de la ville - un Spider-Man à plein temps - mais à mesure que les responsabilités s\'intensifient, la pression provoque une transformation physique surprenante qui menace son existence, tandis qu\'une étrange nouvelle vague de crimes donne naissance à l\'une des menaces les plus redoutables qu\'il ait jamais affrontées.','2026-07-29','02:30:00','tmdb-6OEtkkzoyusZDm2uMJojlWZSLR9.jpg'),(8,1058424,'Hope','Le chef de la police de Hope Harbor, un petit village situé près de la zone coréenne démilitarisée, enquêtee sur le cas d\'une vache morte portant des blessures béantes. Des chasseurs affirment qu\'un tigre errant serait responsable de l\'attaque, mais ils découvrent plus tard d\'autres bovins morts ainsi que le corps du propriétaire de la ferme voisine...','2026-07-15','02:40:00','tmdb-tGdFDoA9qyt0t6Qr9MFnOl8Z2Jz.jpg'),(9,687163,'Projet Dernière Chance','Le professeur de sciences Ryland Grace se réveille à bord d’un vaisseau spatial, à des années-lumière de la Terre, sans aucun souvenir de qui il est ni de la façon dont il est arrivé là. À mesure que sa mémoire lui revient, il commence à découvrir sa mission : résoudre l’énigme d’une mystérieuse substance qui provoque l’extinction du Soleil. Il doit faire appel à ses connaissances scientifiques et ses idées peu orthodoxes pour sauver toute vie sur Terre mais une amitié inattendue pourrait signifier qu’il n’aura pas à accomplir cette tâche seul.','2026-03-15','02:35:00','tmdb-wzyy8ZrsuHfAt4iz4iH3rT0tdoT.jpg'),(10,1616687,'Never Surrender','Quand un gang meurtrier prend d\'assaut sa maison isolée, un vétéran rongé par le remords et ses petits-enfants qu\'il connaît à peine unissent leurs forces pour survivre.','2026-11-11','02:20:00','tmdb-nkE9esa0p8vBmY38jQasmVVRqoc.jpg'),(11,1813355,'La Légende de Derry','Des centaines d’années avant Pennywise, trois jeunes s’aventurent dans la forêt de l’Ouest où ils rencontrent un esprit ancien terrifiant.','2026-10-31','00:29:00','tmdb-95jDCfMC1MNXowaXxPpHuWP5zAr.jpg'),(12,1244523,'Beware Boiúna','Au cœur de l\'Amazonie, une mission médicale se transforme en une lutte pour la survie lorsque Boiúna, un prédateur ancestral colossal, se réveille pour reconquérir son territoire. Dans cette jungle où la nature règne en maître, c\'est elle qui décide qui vivra et qui mourra.','2026-10-01','01:43:00','tmdb-zvDKEHLhr6bk4Gxa9oj0DIkWNJh.jpg'),(13,1003596,'Avengers : Doomsday','Les Avengers, les Wakandais, les Quatre Fantastiques, les New Avengers et les X-Men, tous héros issus de trois univers distincts se retrouvent sur une trajectoire de collision mortelle et font face à une menace existentielle sans précédent, comme ils n\'en ont jamais rencontré auparavant, le Docteur Doom.','2026-12-16','02:45:00','tmdb-cCUxE6EJ9zi1YfdsxU8rWLgqQnN.jpg'),(14,1300968,'Hunger Games : Lever de soleil sur la moisson','Alors que le jour se lève sur les 50e Hunger Games annuels, la peur s\'empare des districts de Panem. Cette année, en l\'honneur du Quarter Quell, deux fois plus de tributs seront tirés au sort. Dans le District 12, Haymitch Abernathy essaie de ne pas trop penser à ses chances. Tout ce qui lui importe est d\'être avec la fille qu\'il aime. Mais lorsque qu\'il est appelé, tous ses rêves s\'effondrent. Il est arraché à sa famille et à son amour, transporté au Capitole avec les trois autres tributs du District 12 : une jeune amie qui est presque une sœur pour lui, un parieur compulsif, et la fille la plus prétentieuse de la ville. Alors que les Jeux commencent, Haymitch comprend qu\'il a été piégé pour échouer. Mais il est prêt à se battre... et faire en sorte que ce combat résonne bien au-delà de l\'arène.','2026-11-18','02:39:00','tmdb-6lwqxIhsKsiz6PM4tzzScn3ok1d.jpg'),(15,1507696,'Apocalypse Z : Nouveau Monde','Depuis l\'apparition du virus TSJ, les îles Canaries sont devenues le dernier refuge en Europe. Alors qu\'une unité part chercher du ravitaillement à Madrid, Roberto se joint à elle avec des intentions troubles. Cernés par les infectés et partagés entre des loyautés contradictoires, ils doivent survivre tout en affrontant un dilemme : suivre les ordres ou tout risquer pour ce qui reste d\'humanité.','2026-10-09','01:38:00','tmdb-hQaxPNX2He3K8bupE0JSX48XNY8.jpg'),(16,1294189,'The Mongoose','Accusé d\'un crime qu\'il n\'a pas commis et n\'ayant plus rien à perdre, un héros de guerre aux prises avec un syndrome de stress post-traumatique entraîne les forces de l\'ordre dans une course-poursuite effrénée à travers tout le pays, avec l\'aide de ses frères d\'armes, tandis qu\'un public captivé encourage sa fuite.','2026-10-29','01:39:00','tmdb-eSS5mvSG84UUuvtbHel5Yu3Wik4.jpg'),(17,1567605,'Scrooge','Ebenezer Scrooge est un vieil homme avare, pingre et solitaire. Bien évidemment, il déteste et méprise Noël et tout ce qui engendre le bonheur. C\'est alors qu\'il reçoit la visite du fantôme d\'un ancien associé ainsi que par les esprits de Noëls du passé, du présent et du futur.','2026-11-11','01:55:00','tmdb-uxYPnpkSmG1iluoSB0fOtYK7BfM.jpg'),(18,656908,'Ramayana : L\'avènement d\'une légende','Une épopée ancienne suit un jeune prince et une jeune princesse dont le mariage et l\'exil qui a suivi marquent le début d\'un voyage légendaire. Leur histoire d\'amour, de devoir et de destin ouvre la voie à de nouveaux défis.','2026-11-05','03:15:00','tmdb-sCqbbDIfLfO52FWjmuVOVZKkVyV.jpg'),(19,1381071,'Godzilla Minus Zero','Japon après la Seconde Guerre mondiale, Shikishima Koichi, un ancien pilote kamikaze survit à une rencontre avec Godzilla et souffre de symptômes de stress post-traumatique. Lorsque la créature réapparaît des années plus tard, un groupe de soldats et de vétérans doit s\'unir pour vaincre le monstre avant qu\'il ne puisse détruire le Japon.','2026-11-03','02:15:00','tmdb-cZrs6whBv4JpPxsNJ2ZefhgUUEF.jpg'),(20,1562642,'Soldat fantôme','Un ingénieur en systèmes d\'armement et ancien béret vert protège ses voisins du Wyoming contre une multinationale impitoyable qui menace leur communauté rurale.','2026-11-06','01:30:00','tmdb-8Y4oJ5iUs3hgPcDB3SIEmsNYjZk.jpg'),(21,1422041,'La bola negra','Espagne 1932, 1937, 2017. Trois hommes. Trois époques. Un même fil invisible de désir, de douleur et d\'héritage. LA BOLA NEGRA traverse le temps pour révéler ce qui les unit.','2026-09-25','02:35:00','tmdb-ttsFxxHKwCHrtAGJt58rpNqHdFw.jpg'),(22,1543469,'Heartland','Misty Jones, ancienne légende de la musique country, sort de sa retraite après la disparition de sa nièce, une chanteuse en pleine ascension. Son enquête va la conduire à affronter son propre passé et découvrir les bas-fonds de Nashville, où les apparences sont trompeuses, et où Misty elle-même n\'est pas celle qu\'elle semble être.','2026-11-13','01:54:00','tmdb-azArOQaVxKa3367XJv2l4ewjsdB.jpg'),(23,NULL,'Crossroads','lucie veut devenir chanteuse','2002-04-03','01:33:00','movie-d32a2b198ed62616.jpg');
/*!40000 ALTER TABLE `movie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movie_director`
--

DROP TABLE IF EXISTS `movie_director`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie_director` (
  `movie_id` int unsigned NOT NULL,
  `director_id` int unsigned NOT NULL,
  KEY `director_id` (`director_id`),
  KEY `movie_id` (`movie_id`),
  CONSTRAINT `movie_director_ibfk_1` FOREIGN KEY (`director_id`) REFERENCES `director` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `movie_director_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movie_director`
--

LOCK TABLES `movie_director` WRITE;
/*!40000 ALTER TABLE `movie_director` DISABLE KEYS */;
INSERT INTO `movie_director` VALUES (1,1),(3,2),(4,1),(5,3),(6,4),(7,5),(8,6),(9,7),(9,8),(10,9),(11,10),(12,11),(13,12),(13,13),(14,14),(15,15),(16,16),(17,17),(18,18),(19,19),(20,20),(21,21),(21,22),(22,23),(23,24);
/*!40000 ALTER TABLE `movie_director` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movie_genre`
--

DROP TABLE IF EXISTS `movie_genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movie_genre` (
  `movie_id` int unsigned NOT NULL,
  `genre_id` int unsigned NOT NULL,
  KEY `genre_id` (`genre_id`),
  KEY `movie_id` (`movie_id`),
  CONSTRAINT `movie_genre_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `movie_genre_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movie_genre`
--

LOCK TABLES `movie_genre` WRITE;
/*!40000 ALTER TABLE `movie_genre` DISABLE KEYS */;
INSERT INTO `movie_genre` VALUES (1,1),(1,3),(3,4),(3,3),(4,4),(4,2),(4,5),(5,6),(6,6),(6,1),(6,4),(7,1),(7,2),(7,4),(8,1),(8,7),(8,2),(9,1),(9,4),(10,2),(10,8),(10,3),(11,9),(11,6),(11,7),(12,6),(12,3),(12,2),(13,1),(13,2),(13,4),(14,1),(14,2),(14,4),(15,2),(15,3),(15,6),(16,2),(16,3),(17,5),(17,10),(18,5),(18,2),(18,4),(19,1),(19,6),(19,2),(20,2),(20,3),(21,9),(21,11),(22,3),(22,7),(23,9),(23,1);
/*!40000 ALTER TABLE `movie_genre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `review` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `rate` tinyint NOT NULL,
  `review` text NOT NULL,
  `approved` tinyint NOT NULL,
  `user_id` int unsigned NOT NULL,
  `movie_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`movie_id`),
  KEY `movie_id` (`movie_id`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (1,4,'A tous les niveaux, Inception flotte à des altitudes que peu de blockbusters peuvent atteindre.',1,1,1),(2,5,'Merci !',1,4,1),(3,4,'bon film',1,5,6),(4,4,'meilleur film',1,5,20);
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'john@gmail.com','$2y$10$WIIdJn.jbqOK30TAzDlqyuu3vBt8NKPgxkYMqJbLG3HWsoNiiBOMm','john','doe','user'),(4,'test@moviz.fr','$2y$10$oWJOaoP83v51A3FDbjbySuoR4um41j.hCpfAkeiAoQMHSbvytOipq','marie','Hubert','admin'),(5,'test2@moviz.fr','$2y$10$HC96UJjpHdMv6BDX.FznU.SX0x86KsvTl2y.6WnbbuKtTP5Jd.KsK','sarah','bou','user'),(6,'test4@moviz.fr','$2y$10$yYFqbzw8ZGqQiZC/UgXjEOwd6gLjHGrX0RwOZ2MaBE2/a/qfb.G86','popo','bou','user'),(7,'test5@moviz.fr','$2y$10$OBhNsFMe0gWe4ufVQdvFDOHDmIm.OhOMB9XjP9AUlQo3XE7b.X9Ru','hiziya','ben','user'),(8,'test6@moviz.fr','$2y$10$MAVd.ytGWk952b2/n3lGMe0jB8Zt87HxSfGWGeNLPbGAcksTdxt/.','hiziya','ben','user');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-21  8:53:02
