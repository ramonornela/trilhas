-- phpMyAdmin SQL Dump
-- version 3.3.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 30, 2010 at 05:10 PM
-- Server version: 5.1.50
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `trilhas`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `begin` date NOT NULL,
  `end` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `activity`
--


-- --------------------------------------------------------

--
-- Table structure for table `activity_text`
--

CREATE TABLE IF NOT EXISTS `activity_text` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `activity_id` bigint(20) NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','final') DEFAULT 'open',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `activity_text`
--


-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

CREATE TABLE IF NOT EXISTS `calendar` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) DEFAULT NULL,
  `description` text NOT NULL,
  `begin` date NOT NULL,
  `end` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `calendar`
--

INSERT INTO `calendar` (`id`, `user_id`, `classroom_id`, `description`, `begin`, `end`) VALUES
(8, 2, 3, 'jkas dfjkadsfkjasflakjsdfkasdf', '2010-09-27', NULL),
(9, 2, NULL, 'aviso geral. todos os cursos estarÃ£o bonitos no dia 10. hehehhe', '2010-09-28', '2010-10-30');

-- --------------------------------------------------------

--
-- Table structure for table `certificate`
--

CREATE TABLE IF NOT EXISTS `certificate` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classroom_id` bigint(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `hours` int(5) NOT NULL,
  `begin` date NOT NULL,
  `end` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `certificate`
--


-- --------------------------------------------------------

--
-- Table structure for table `certificate_user`
--

CREATE TABLE IF NOT EXISTS `certificate_user` (
  `certificate_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`certificate_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `certificate_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sender` bigint(20) NOT NULL,
  `receiver` bigint(20) NOT NULL,
  `description` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sender` (`sender`),
  KEY `receiver` (`receiver`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chat`
--


-- --------------------------------------------------------

--
-- Table structure for table `chat_room`
--

CREATE TABLE IF NOT EXISTS `chat_room` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classroom_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `begin` date NOT NULL,
  `end` date DEFAULT NULL,
  `max_student` int(10) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chat_room`
--


-- --------------------------------------------------------

--
-- Table structure for table `chat_room_message`
--

CREATE TABLE IF NOT EXISTS `chat_room_message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `chat_room_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `description` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `chat_room_id` (`chat_room_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `chat_room_message`
--


-- --------------------------------------------------------

--
-- Table structure for table `classroom`
--

CREATE TABLE IF NOT EXISTS `classroom` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) NOT NULL,
  `responsible` bigint(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `begin` date NOT NULL,
  `end` date DEFAULT NULL,
  `max_student` int(10) DEFAULT NULL,
  `amount` decimal(20,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `responsible` (`responsible`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `classroom`
--

INSERT INTO `classroom` (`id`, `course_id`, `responsible`, `name`, `begin`, `end`, `max_student`, `amount`, `status`) VALUES
(3, 4, 4, 'Open Primeiro', '2010-09-29', NULL, 40, NULL, 'active'),
(4, 5, 2, 'Open Segundo', '2010-09-27', NULL, NULL, NULL, 'active'),
(5, 6, NULL, 'Open Terceiro', '2010-09-27', NULL, NULL, NULL, 'active'),
(6, 4, 2, 'Longa', '2010-09-30', '2010-10-30', 10, '100.00', 'open'),
(7, 4, 2, 'Curta', '2010-09-28', '2010-07-10', 2, '0.00', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `classroom_user`
--

CREATE TABLE IF NOT EXISTS `classroom_user` (
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) NOT NULL,
  PRIMARY KEY (`user_id`,`classroom_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `classroom_user`
--

INSERT INTO `classroom_user` (`user_id`, `classroom_id`) VALUES
(3, 7),
(4, 7);

-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classroom_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `configuration`
--


-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) NOT NULL,
  `content_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `position` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `content`
--


-- --------------------------------------------------------

--
-- Table structure for table `content_access`
--

CREATE TABLE IF NOT EXISTS `content_access` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `content_access`
--


-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE IF NOT EXISTS `course` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `responsible` bigint(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Uncategorized',
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `responsible` (`responsible`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `user_id`, `responsible`, `name`, `description`, `image`, `category`, `status`, `created`) VALUES
(4, 2, 4, 'Primeiro', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. \r\n\r\nLorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \r\n\r\nIt has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '4c98d8c67b7ad', 'um', 'active', '2010-09-21 13:09:42'),
(5, 2, 2, 'Segundo', 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.', NULL, '', 'active', '2010-09-27 19:47:44'),
(6, 2, 2, 'Terceiro', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\nThere are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.', '4ca1483b21633', '', 'active', '2010-09-27 22:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `exercise`
--

CREATE TABLE IF NOT EXISTS `exercise` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `classroom_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `time` int(10) DEFAULT NULL,
  `begin` date NOT NULL,
  `end` date DEFAULT NULL,
  `attempts` bigint(20) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `exercise`
--


-- --------------------------------------------------------

--
-- Table structure for table `exercise_answer`
--

CREATE TABLE IF NOT EXISTS `exercise_answer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `exercise_value_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `exercise_value_id` (`exercise_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `exercise_answer`
--


-- --------------------------------------------------------

--
-- Table structure for table `exercise_note`
--

CREATE TABLE IF NOT EXISTS `exercise_note` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `exercise_id` bigint(20) NOT NULL,
  `note` decimal(4,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `exercise_id` (`exercise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `exercise_note`
--


-- --------------------------------------------------------

--
-- Table structure for table `exercise_question`
--

CREATE TABLE IF NOT EXISTS `exercise_question` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `exercise_id` bigint(20) NOT NULL,
  `label` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `note` decimal(4,2) DEFAULT NULL,
  `position` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_id` (`exercise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `exercise_question`
--


-- --------------------------------------------------------

--
-- Table structure for table `exercise_value`
--

CREATE TABLE IF NOT EXISTS `exercise_value` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `exercise_question_id` bigint(20) NOT NULL,
  `value` varchar(255) NOT NULL,
  `justify` text,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_question_id` (`exercise_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `exercise_value`
--


-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `user_id`, `classroom_id`, `question`, `answer`) VALUES
(1, 2, 3, 'pq? pqqqq??', 'uai, pq sim! funfa'),
(2, 2, 3, 'outra pergunta veihhhhhh?', 'serio! outra resposta, hehehhe!');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`id`, `user_id`, `classroom_id`, `name`, `location`, `created`) VALUES
(2, 2, 3, 'poaus dpfoiu paosdfa', '4c9cf52468077', '2010-09-24 15:59:48'),
(3, 2, 3, 'eqwryoq weyrqwerqyower qoiwer oiquew', '4c9cf54409176', '2010-09-24 16:00:20'),
(4, 2, 3, 'caracas', '4c9cf55916c3d', '2010-09-24 16:00:41');

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

CREATE TABLE IF NOT EXISTS `forum` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `begin` date NOT NULL,
  `end` date DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `forum`
--

INSERT INTO `forum` (`id`, `user_id`, `classroom_id`, `title`, `description`, `begin`, `end`, `created`, `status`) VALUES
(1, 2, 3, 'deixa eu te amar', 'faz de conta que sou o primeiro', '2010-09-24', '2010-11-30', '2010-09-24 22:44:30', 'inactive'),
(4, 2, 7, 'Novo design', 'Adorei esse novo design. Super clean e elegante!', '2010-09-28', NULL, '2010-09-28 23:12:45', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `forum_reply`
--

CREATE TABLE IF NOT EXISTS `forum_reply` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `forum_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `forum_id` (`forum_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `forum_reply`
--

INSERT INTO `forum_reply` (`id`, `forum_id`, `user_id`, `description`, `created`) VALUES
(6, 1, 2, 'akljdsf lkjfajdfklalkdfhalkjdshflka', '2010-09-24 23:46:37'),
(9, 1, 2, 'kljas fkjlaslkfjasdf\n\n\nasdflk asdfjalkdflkahdsf\n\nasdfja sdfhakljdflkjasdf', '2010-09-25 00:10:17'),
(11, 4, 3, 'Cara! TambÃ©m gostei muito...a', '2010-09-28 23:19:18'),
(13, 4, 3, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\n\n\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '2010-09-28 23:29:32');

-- --------------------------------------------------------

--
-- Table structure for table `glossary`
--

CREATE TABLE IF NOT EXISTS `glossary` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) NOT NULL,
  `word` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`),
  KEY `user_id_2` (`user_id`),
  KEY `classroom_id_2` (`classroom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `glossary`
--

INSERT INTO `glossary` (`id`, `user_id`, `classroom_id`, `word`, `description`, `created`) VALUES
(2, 2, 3, 'novo alter asdf', 'asjlkdfh kajlsdflkjad kfljasdf askdfhj kalsdhf kljahds lkjhads klfjasd\nf\nasdfkl;a sdlkf akljsdf\n\nasdlkjfh alkjsdfkaasd.,fa sdflkadsf\nasdlkjf klasjdhf lkja dsklfakljsdhklajsdfjk', '2010-09-22 17:45:58'),
(3, 2, 3, 'qwerqwerq wer qwe rqe', 'asdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhj', '2010-09-22 17:58:12'),
(4, 2, 3, 'caracas', 'sdfla;sd fadsfljalkdfkadhjfs', '2010-09-22 18:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) DEFAULT NULL,
  `module` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `log`
--


-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sender` bigint(20) NOT NULL,
  `receiver` bigint(20) NOT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sender` (`sender`),
  KEY `receiver` (`receiver`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `sender`, `receiver`, `description`, `created`) VALUES
(1, 2, 2, 'adfs as df asd fa', '2010-09-28 23:44:31'),
(2, 2, 2, 'oi pfuqwoieruqiopuweropiqpoeroqpe', '2010-09-28 23:44:42'),
(3, 2, 4, 'blablabaaablaal aaaaa', '2010-09-29 00:04:24'),
(4, 2, 4, 'caracas aaa', '2010-09-29 00:04:55'),
(5, 4, 4, 'opa valeu!', '2010-09-29 00:08:30'),
(6, 4, 3, 'ajskdf adfjkalksdfakldsfa', '2010-09-29 00:10:32');

-- --------------------------------------------------------

--
-- Table structure for table `notepad`
--

CREATE TABLE IF NOT EXISTS `notepad` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classroom_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `classroom_id` (`classroom_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `notepad`
--

INSERT INTO `notepad` (`id`, `classroom_id`, `user_id`, `description`, `created`) VALUES
(9, 3, 2, 'asfdjn akjlsfljasdfkakldshalkdsfasdf klajsdfkjlahds kljfa kldsf kaldsf', '2010-09-23 09:12:56'),
(10, 3, 2, 'asdf', '2010-09-23 09:12:59'),
(11, 3, 2, 'qwer', '2010-09-23 09:13:02'),
(12, 3, 2, 'ZXvzxcv', '2010-09-23 09:13:05'),
(14, 7, 2, 'oiiiiieeeee', '2010-09-28 23:42:00'),
(15, 7, 2, 'as fa sdf as df asdfa', '2010-09-28 23:43:05'),
(16, 7, 2, 'sdfghjkl,;.''', '2010-09-28 23:44:02');

-- --------------------------------------------------------

--
-- Table structure for table `panel`
--

CREATE TABLE IF NOT EXISTS `panel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classroom_id` bigint(20) NOT NULL,
  `type` enum('exercise','forum','activity') NOT NULL,
  `item_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `classroom_id` (`classroom_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `panel`
--


-- --------------------------------------------------------

--
-- Table structure for table `panel_note`
--

CREATE TABLE IF NOT EXISTS `panel_note` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `panel_id` bigint(20) DEFAULT NULL,
  `note` decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `panel_id` (`panel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `panel_note`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sex` enum('M','F') DEFAULT 'M',
  `born` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'student',
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `sex`, `born`, `email`, `password`, `role`, `description`, `image`, `created`, `status`) VALUES
(2, 'admin', 'M', '2010-09-20', 'abdala.cerqueira@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'institution', '', '4ca148aa6cf12', '2010-09-21 12:51:00', 'active'),
(3, 'Pedro Silva Pereira', 'M', '2010-02-09', 'pedro@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'student', '', '4ca128fcd77b8', '2010-09-27 20:30:04', 'active'),
(4, 'Rafael Alves Costa', 'M', '1987-12-04', 'rcosta@gmail.com', '202cb962ac59075b964b07152d234b70', 'teacher', '', '4ca2211232ca9', '2010-09-28 14:08:34', 'active');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `activity_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `activity_text`
--
ALTER TABLE `activity_text`
  ADD CONSTRAINT `activity_text_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `activity_text_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `calendar`
--
ALTER TABLE `calendar`
  ADD CONSTRAINT `calendar_ibfk_3` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `calendar_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `calendar_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `certificate`
--
ALTER TABLE `certificate`
  ADD CONSTRAINT `certificate_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `certificate_user`
--
ALTER TABLE `certificate_user`
  ADD CONSTRAINT `certificate_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `certificate_user_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`id`);

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `user` (`id`);

--
-- Constraints for table `chat_room`
--
ALTER TABLE `chat_room`
  ADD CONSTRAINT `chat_room_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `chat_room_message`
--
ALTER TABLE `chat_room_message`
  ADD CONSTRAINT `chat_room_message_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `chat_room_message_ibfk_1` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_room` (`id`);

--
-- Constraints for table `classroom`
--
ALTER TABLE `classroom`
  ADD CONSTRAINT `classroom_ibfk_3` FOREIGN KEY (`responsible`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `classroom_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  ADD CONSTRAINT `classroom_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`);

--
-- Constraints for table `classroom_user`
--
ALTER TABLE `classroom_user`
  ADD CONSTRAINT `classroom_user_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `classroom_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `configuration`
--
ALTER TABLE `configuration`
  ADD CONSTRAINT `configuration_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_2` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`),
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`);

--
-- Constraints for table `content_access`
--
ALTER TABLE `content_access`
  ADD CONSTRAINT `content_access_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `content_access_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`);

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`responsible`) REFERENCES `user` (`id`);

--
-- Constraints for table `exercise`
--
ALTER TABLE `exercise`
  ADD CONSTRAINT `exercise_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `exercise_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `exercise_answer`
--
ALTER TABLE `exercise_answer`
  ADD CONSTRAINT `exercise_answer_ibfk_2` FOREIGN KEY (`exercise_value_id`) REFERENCES `exercise_value` (`id`),
  ADD CONSTRAINT `exercise_answer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `exercise_note`
--
ALTER TABLE `exercise_note`
  ADD CONSTRAINT `exercise_note_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`id`),
  ADD CONSTRAINT `exercise_note_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `exercise_question`
--
ALTER TABLE `exercise_question`
  ADD CONSTRAINT `exercise_question_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`id`);

--
-- Constraints for table `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `faq_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `forum_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `forum_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `forum_reply`
--
ALTER TABLE `forum_reply`
  ADD CONSTRAINT `forum_reply_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `forum_reply_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`id`);

--
-- Constraints for table `glossary`
--
ALTER TABLE `glossary`
  ADD CONSTRAINT `glossary_ibfk_3` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `glossary_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `glossary_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `user` (`id`);

--
-- Constraints for table `notepad`
--
ALTER TABLE `notepad`
  ADD CONSTRAINT `notepad_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `notepad_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `panel`
--
ALTER TABLE `panel`
  ADD CONSTRAINT `panel_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `panel_note`
--
ALTER TABLE `panel_note`
  ADD CONSTRAINT `panel_note_ibfk_2` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`),
  ADD CONSTRAINT `panel_note_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
