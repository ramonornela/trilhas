# Sequel Pro dump
# Version 2492
# http://code.google.com/p/sequel-pro
#
# Host: 127.0.0.1 (MySQL 5.1.51)
# Database: trails
# Generation Time: 2010-12-03 17:44:43 -0200
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table activity
# ------------------------------------------------------------


# Dump of table activity_text
# ------------------------------------------------------------


# Dump of table calendar
# ----------------------------------------------------------


# Dump of table certificate
# ------------------------------------------------------------



# Dump of table chat
# ------------------------------------------------------------



# Dump of table chat_room
# ------------------------------------------------------------


# Dump of table chat_room_message
# ------------------------------------------------------------


# Dump of table classroom
# ------------------------------------------------------------

LOCK TABLES `classroom` WRITE;
/*!40000 ALTER TABLE `classroom` DISABLE KEYS */;
INSERT INTO `classroom` (`id`,`course_id`,`responsible`,`name`,`begin`,`end`,`max_student`,`amount`,`status`)
VALUES
	(3,4,4,'Open Primeiro','2010-09-29',NULL,40,NULL,'active'),
	(4,5,2,'Open Segundo','2010-09-27',NULL,NULL,NULL,'active'),
	(5,6,NULL,'Open Terceiro','2010-09-27',NULL,NULL,NULL,'active'),

/*!40000 ALTER TABLE `classroom` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table classroom_user
# ------------------------------------------------------------

LOCK TABLES `classroom_user` WRITE;
/*!40000 ALTER TABLE `classroom_user` DISABLE KEYS */;
INSERT INTO `classroom_user` (`user_id`,`classroom_id`,`updated`,`status`)
VALUES
	(2,5,'2010-12-03 15:37:47','registered'),
	(2,8,'2010-12-03 17:23:41','registered'),
	(3,5,'2010-12-03 17:05:50','registered'),

/*!40000 ALTER TABLE `classroom_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table configuration
# ------------------------------------------------------------



# Dump of table content
# ------------------------------------------------------------

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
INSERT INTO `content` (`id`,`course_id`,`content_id`,`title`,`description`,`position`)
VALUES
	(1,4,NULL,'Introdução','<p>\n	It is a long <strong><em>established</em></strong> fact that a reader will be <strong>distracted</strong> by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using &#39;Content here, content here&#39;, making it look like readable English. Many desktop <span style=\"background-color: rgb(255, 255, 0);\">publishing</span> packages and web page editors now use Lorem Ipsum as their default model text, and a search for &#39;lorem ipsum&#39; will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by <span style=\"color: rgb(165, 42, 42);\"><em>accident</em></span>, sometimes on purpose (injected humour and the like). as df a&#39;sdf alsdfja s dfkadsf</p>\n<p>\n	asdf ;kasd fja sdfjaldjfadsf as df as df a sdf</p>',1),
	(2,4,NULL,'Modulo 1','It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n',2),
	(3,4,2,'Pagina 1','It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).',1),
	(4,6,NULL,'Introdução','Bem vindo ao curso!',0),
	(5,5,NULL,'Introdução','Bem vindo ao curso!',0),

/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table content_access
# ------------------------------------------------------------


# Dump of table content_file
# ------------------------------------------------------------



# Dump of table content_template
# ------------------------------------------------------------



# Dump of table course
# ------------------------------------------------------------

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
INSERT INTO `course` (`id`,`user_id`,`responsible`,`name`,`description`,`hours`,`image`,`category`,`status`,`created`)
VALUES
	(4,2,4,'Primeiro','Lorem Ipsum is simply dummy text of the printing and typesetting industry. \r\n\r\nLorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n\r\nIt has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',0,'','um','active','2010-09-21 13:09:42'),
	(5,2,2,'Segundo','Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. ',0,NULL,'','active','2010-09-27 19:47:44'),
	(6,2,2,'Terceiro','It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\nThere are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.',0,'','','active','2010-09-27 22:42:37');

/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exercise
# ------------------------------------------------------------


# Dump of table exercise_answer
# ------------------------------------------------------------



# Dump of table exercise_note
# ------------------------------------------------------------


# Dump of table exercise_option
# ------------------------------------------------------------


# Dump of table exercise_question
# ------------------------------------------------------------


# Dump of table faq
# ------------------------------------------------------------


# Dump of table file
# ------------------------------------------------------------


# Dump of table forum
# ------------------------------------------------------------


# Dump of table forum_reply
# ------------------------------------------------------------


# Dump of table glossary
# ------------------------------------------------------------


# Dump of table log
# ------------------------------------------------------------



# Dump of table message
# ------------------------------------------------------------


# Dump of table notepad
# ------------------------------------------------------------


# Dump of table page
# ------------------------------------------------------------



# Dump of table panel
# ------------------------------------------------------------


# Dump of table panel_note
# ------------------------------------------------------------


# Dump of table restriction_panel
# ------------------------------------------------------------


# Dump of table restriction_time
# ------------------------------------------------------------


# Dump of table selection_process
# ------------------------------------------------------------



# Dump of table selection_process_classroom
# ------------------------------------------------------------



# Dump of table selection_process_user
# ------------------------------------------------------------


# Dump of table timeline
# ------------------------------------------------------------



# Dump of table user
# ------------------------------------------------------------

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`,`name`,`sex`,`born`,`email`,`password`,`role`,`description`,`image`,`created`,`status`)
VALUES
	(2,'Institution','M','1987-12-04','institution@institution.com','202cb962ac59075b964b07152d234b70','institution','','','2010-09-21 12:51:00','active'),
	(3,'Student','M','1987-12-04','student@student.com','202cb962ac59075b964b07152d234b70','student','','','2010-09-27 20:30:04','active'),
	(4,'Teacher','M','1987-12-04','teacher@teacher.com','202cb962ac59075b964b07152d234b70','teacher','','','2010-09-28 14:08:34','active'),
	(5,'Coordinator','M','1987-12-04','coordinator@coordinator.com','202cb962ac59075b964b07152d234b70','coordinator','','','2010-09-28 14:08:34','active');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
