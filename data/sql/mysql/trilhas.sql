-- phpMyAdmin SQL Dump
-- version 3.3.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 07, 2010 at 02:02 AM
-- Server version: 5.1.50
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
  `title` varchar(255) NOT NULL,
  `description` text,
  `begin` date NOT NULL,
  `end` date DEFAULT NULL,
  `status` enum('active','inactive','final') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`id`, `user_id`, `classroom_id`, `title`, `description`, `begin`, `end`, `status`) VALUES
(1, 2, 7, 'Atividade 1', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', '2010-09-30', '2010-10-06', 'active'),
(2, 4, 3, 'Texto sobre isso', 'Caracas  em lembrava disso', '2010-09-21', '2010-12-30', 'active'),
(3, 2, 7, 'caracas', 'sadf a sdf adsf', '2010-10-18', '2010-10-27', 'active'),
(4, 2, 7, 'asdfadsf', 'asdfasdf', '2010-11-03', '2010-11-04', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `activity_text`
--

CREATE TABLE IF NOT EXISTS `activity_text` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `sender` bigint(20) NOT NULL,
  `activity_id` bigint(20) NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','final','close') DEFAULT 'open',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_id` (`activity_id`),
  KEY `sender` (`sender`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `activity_text`
--

INSERT INTO `activity_text` (`id`, `user_id`, `sender`, `activity_id`, `description`, `status`, `created`) VALUES
(1, 2, 2, 1, 'ajs fjakjlsdf kjahsd fkja kdjlsfh akjf lkja sdf', 'open', '2010-09-30 22:06:05'),
(2, 2, 2, 1, 'asd f asdf a sdf a sdf a sdfasdf asjdkfh asjkldfhajlsdf klahd fkljha klsdjfa', 'open', '2010-09-30 22:11:13'),
(3, 2, 2, 1, 'asd f asdf a sdf a sdf a sdfasdf asjdkfh asjkldfhajlsdf klahd fkljha klsdjfa\n\nfasdfasdf asdf asf asdf asdf asdf\nasdfnakjsdf alkdjsfadsfgjasdf\n\n\n asdf asdf asdf asdf asdf adsf\n\n\n asdf adsf asdf asdf adsf asdf asdf asdf asdf \n\n \nasdf asdf adsf afds', 'final', '2010-09-30 22:19:31'),
(4, 2, 2, 1, 'asd f asdf a sdf a sdf a sdfasdf asjdkfh asjkldfhajlsdf klahd fkljha klsdjfa\n\nfasdfasdf asdf asf asdf asdf asdf\nasdfnakjsdf alkdjsfadsfgjasdf\n\n\n asdf asdf asdf asdf asdf adsf\n\n\n asdf adsf asdf asdf adsf asdf asdf asdf asdf \n\n \nasdf asdf adsf afdsl;ksaj f;klajsdlfk;als dfadsf', 'open', '2010-09-30 23:08:01'),
(5, 3, 3, 1, 'slkjdfh alkjsdfkljafl kajdf kjlaldfja kdsfkadsf', 'open', '2010-09-30 23:12:11'),
(6, 3, 2, 1, 'slkjdfh alkjsdfkljafl kajdf kjlaldfja kdsfkadsfsa;dkf lkajsdflajlksdfasdf\n\nasdfasdfb ajkdgsfafgkjasdf', 'open', '2010-09-30 23:12:23'),
(7, 3, 4, 1, 'slkjdfh alkjsdfkljafl kajdf kjlaldfja kdsfkadsfsa;dkf lkajsdflajlksdfasdf\n\nasdfasdfb ajkdgsfafgkjasdf\n\n;adslkf asfkadf jk aldskf akjsfl kajsdfkjlha lksdjfhladks f\\a\ns\nasdf adlskfjaklsdf kjaskdfakjshdf kjadhjksf\n\nasdj kfakjsdhf kaldkflakhdsf', 'open', '2010-09-30 23:16:24'),
(8, 3, 3, 1, 'slkjdfh alkjsdfkljafl kajdf kjlaldfja kdsfkadsfsa;dkf lkajsdflajlksdfasdf\n\nasdfasdfb ajkdgsfafgkjasdf\n\n;adslkf asfkadf jk aldskf akjsfl kajsdfkjlha lksdjfhladks f\\a\ns\nasdf adlskfjaklsdf kjaskdfakjshdf kjadhjksf\n\nasdj kfakjsdhf kaldkflakhdsf', 'open', '2010-09-30 23:22:36'),
(9, 2, 2, 3, 'a sdjfa ldfkjadfkakdljflakdf', 'open', '2010-10-18 19:50:21'),
(10, 2, 2, 3, 'a sdjfa ldfkjadfkakdljflakdf.,asmdnf,.amndfjkal dfjakdf lkafjdkladf', 'open', '2010-10-18 19:50:26'),
(11, 4, 4, 3, 'ajklsh lkfjdfjakjlsdf akljhdf lkahdf ka dkfjha lkdfjkad fkljha lksdf kajds flk laksdf', 'open', '2010-10-18 19:51:11'),
(12, 4, 4, 3, 'ajklsh lkfjdfjakjlsdf akljhdf lkahdf ka dkfjha lkdfjkad \n\nfkljha lksdf kajds flk laksdf\n\nasd,f am;sd fasdf', 'open', '2010-10-18 19:51:18'),
(13, 2, 2, 4, 'asd.jfh akjlsfkjadsfkjaskldahsdf', 'open', '2010-11-03 14:17:29'),
(14, 2, 2, 4, 'asd.jfh akjlsfkjadsfkjaskldahsdfa.djf asdfasdljkf ajlsdhgf kajshdg fahjgsd jfhasdf', 'open', '2010-11-03 14:17:40');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `calendar`
--

INSERT INTO `calendar` (`id`, `user_id`, `classroom_id`, `description`, `begin`, `end`) VALUES
(8, 2, 3, 'jkas dfjkadsfkjasflakjsdfkasdf', '2010-09-27', NULL),
(9, 4, NULL, 'aviso geral. todos os cursos estarão bonitos no dia 10. hehehhe', '2010-09-28', '2010-10-30'),
(10, 2, 4, 'basf ajskdfklajdfshjkl', '2010-11-03', '2010-11-04');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `course_id`, `content_id`, `title`, `description`, `position`) VALUES
(1, 4, NULL, 'Introdução', '<p>\n	It is a long <strong><em>established</em></strong> fact that a reader will be <strong>distracted</strong> by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using &#39;Content here, content here&#39;, making it look like readable English. Many desktop <span style="background-color: rgb(255, 255, 0);">publishing</span> packages and web page editors now use Lorem Ipsum as their default model text, and a search for &#39;lorem ipsum&#39; will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by <span style="color: rgb(165, 42, 42);"><em>accident</em></span>, sometimes on purpose (injected humour and the like). as df a&#39;sdf alsdfja s dfkadsf</p>\n<p>\n	asdf ;kasd fja sdfjaldjfadsf as df as df a sdf</p>', 1),
(2, 4, NULL, 'Modulo 1', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n', 2),
(3, 4, 2, 'Pagina 1', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', 1),
(4, 6, NULL, 'Introdução', 'Bem vindo ao curso!', 0),
(5, 5, NULL, 'Introdução', 'Bem vindo ao curso!', 0),
(6, 4, 2, 'pagina 2', '<p>\n	sdfghjkloiuytredfgm, askjdf alsdfljadhjsf ajkhsdf jadsf asdkjfaklsd flja dlsjfafds af asd fa sdf asdf kasdf ljadasdf as df asdf asad f asdf as dfasd</p>', 2),
(7, 4, 2, 'pagina 3', '<p>\n	asdf kljasljdfha kljdshf kljahd fkjladkjlhakljsdhfa</p>', 3),
(8, 4, NULL, 'Modulo 2', '<p>\n	as fd sd fa sdf</p>', 4),
(9, 4, NULL, 'Modulo 3', '<p>\n	as fd sd fa sdf</p>', 3);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=503 ;

--
-- Dumping data for table `content_access`
--

INSERT INTO `content_access` (`id`, `content_id`, `user_id`) VALUES
(1, 1, 4),
(2, 1, 4),
(3, 2, 4),
(4, 3, 4),
(5, 2, 4),
(6, 3, 4),
(7, 1, 4),
(8, 1, 4),
(9, 2, 4),
(10, 1, 4),
(11, 1, 4),
(12, 1, 4),
(13, 2, 4),
(14, 1, 4),
(15, 2, 4),
(16, 3, 4),
(17, 2, 4),
(18, 1, 4),
(19, 2, 4),
(20, 3, 4),
(21, 2, 4),
(22, 1, 4),
(23, 1, 4),
(24, 1, 4),
(25, 1, 4),
(26, 1, 4),
(27, 2, 4),
(28, 3, 4),
(29, 1, 4),
(30, 1, 4),
(31, 1, 4),
(32, 1, 4),
(33, 2, 4),
(34, 3, 4),
(35, 2, 4),
(36, 3, 4),
(37, 2, 4),
(38, 3, 4),
(39, 2, 4),
(40, 3, 4),
(41, 2, 4),
(42, 1, 4),
(43, 2, 4),
(44, 3, 4),
(45, 2, 4),
(46, 1, 4),
(47, 2, 4),
(48, 3, 4),
(49, 2, 4),
(50, 3, 4),
(51, 2, 4),
(52, 1, 4),
(53, 2, 4),
(54, 3, 4),
(55, 2, 4),
(56, 3, 4),
(57, 2, 4),
(58, 1, 4),
(59, 2, 4),
(60, 3, 4),
(61, 2, 4),
(62, 1, 4),
(63, 2, 4),
(64, 3, 4),
(65, 2, 4),
(66, 1, 4),
(67, 2, 4),
(68, 3, 4),
(69, 2, 4),
(70, 1, 4),
(71, 2, 4),
(72, 3, 4),
(73, 2, 4),
(74, 1, 4),
(75, 2, 4),
(76, 3, 4),
(77, 2, 4),
(78, 1, 4),
(79, 2, 4),
(80, 3, 4),
(81, 1, 4),
(82, 4, 2),
(83, 5, 2),
(84, 1, 2),
(85, 2, 2),
(86, 3, 2),
(87, 2, 2),
(88, 1, 2),
(89, 2, 2),
(90, 3, 2),
(91, 2, 2),
(92, 1, 2),
(93, 2, 2),
(94, 3, 2),
(95, 2, 2),
(96, 1, 2),
(97, 2, 2),
(98, 3, 2),
(99, 2, 2),
(100, 1, 2),
(101, 5, 2),
(102, 1, 2),
(103, 1, 4),
(104, 2, 4),
(105, 3, 4),
(106, 2, 4),
(107, 1, 4),
(108, 1, 4),
(109, 1, 2),
(110, 2, 2),
(111, 3, 2),
(112, 2, 2),
(113, 3, 2),
(114, 2, 2),
(115, 1, 2),
(116, 2, 2),
(117, 3, 2),
(118, 2, 2),
(119, 1, 2),
(120, 1, 2),
(121, 1, 2),
(122, 1, 2),
(123, 1, 2),
(124, 1, 2),
(125, 1, 2),
(126, 1, 2),
(127, 2, 2),
(128, 3, 2),
(129, 2, 2),
(130, 1, 2),
(131, 1, 2),
(132, 1, 2),
(133, 1, 2),
(134, 1, 2),
(135, 1, 2),
(136, 1, 2),
(137, 1, 2),
(138, 1, 2),
(139, 1, 2),
(140, 1, 2),
(141, 1, 2),
(142, 1, 2),
(143, 1, 2),
(144, 2, 2),
(145, 3, 2),
(146, 1, 2),
(147, 1, 2),
(148, 1, 2),
(149, 1, 2),
(150, 1, 2),
(151, 1, 2),
(152, 1, 2),
(153, 1, 2),
(154, 1, 2),
(155, 1, 2),
(156, 1, 2),
(157, 2, 2),
(158, 3, 2),
(159, 2, 2),
(160, 1, 2),
(161, 2, 2),
(162, 3, 2),
(163, 2, 2),
(164, 1, 2),
(165, 1, 2),
(166, 1, 2),
(167, 1, 2),
(168, 1, 2),
(169, 1, 2),
(170, 1, 2),
(171, 2, 2),
(172, 1, 2),
(173, 2, 2),
(174, 3, 2),
(175, 7, 2),
(176, 6, 2),
(177, 9, 2),
(178, 8, 2),
(179, 1, 2),
(180, 2, 2),
(181, 3, 2),
(182, 7, 2),
(183, 6, 2),
(184, 9, 2),
(185, 8, 2),
(186, 9, 2),
(187, 6, 2),
(188, 7, 2),
(189, 3, 2),
(190, 2, 2),
(191, 1, 2),
(192, 2, 2),
(193, 3, 2),
(194, 7, 2),
(195, 6, 2),
(196, 9, 2),
(197, 8, 2),
(198, 9, 2),
(199, 6, 2),
(200, 7, 2),
(201, 3, 2),
(202, 2, 2),
(203, 1, 2),
(204, 1, 2),
(205, 2, 2),
(206, 3, 2),
(207, 7, 2),
(208, 6, 2),
(209, 9, 2),
(210, 8, 2),
(211, 9, 2),
(212, 6, 2),
(213, 9, 2),
(214, 8, 2),
(215, 9, 2),
(216, 6, 2),
(217, 7, 2),
(218, 6, 2),
(219, 9, 2),
(220, 6, 2),
(221, 9, 2),
(222, 6, 2),
(223, 9, 2),
(224, 6, 2),
(225, 1, 2),
(226, 2, 2),
(227, 3, 2),
(228, 3, 2),
(229, 3, 2),
(230, 3, 2),
(231, 2, 2),
(232, 3, 2),
(233, 1, 2),
(234, 2, 2),
(235, 3, 2),
(236, 1, 2),
(237, 2, 2),
(238, 1, 2),
(239, 2, 2),
(240, 1, 2),
(241, 1, 2),
(242, 1, 2),
(243, 1, 2),
(244, 1, 2),
(245, 2, 2),
(246, 3, 2),
(247, 7, 2),
(248, 7, 2),
(249, 3, 2),
(250, 2, 2),
(251, 3, 2),
(252, 7, 2),
(253, 7, 2),
(254, 3, 2),
(255, 2, 2),
(256, 1, 2),
(257, 1, 2),
(258, 1, 2),
(259, 1, 2),
(260, 1, 2),
(261, 2, 2),
(262, 1, 2),
(263, 1, 2),
(264, 1, 2),
(265, 1, 2),
(266, 2, 2),
(267, 3, 2),
(268, 2, 2),
(269, 3, 2),
(270, 7, 2),
(271, 6, 2),
(272, 9, 2),
(273, 6, 2),
(274, 7, 2),
(275, 3, 2),
(276, 2, 2),
(277, 1, 2),
(278, 3, 2),
(279, 2, 2),
(280, 1, 2),
(281, 2, 2),
(282, 3, 2),
(283, 7, 2),
(284, 6, 2),
(285, 9, 2),
(286, 8, 2),
(287, 9, 2),
(288, 6, 2),
(289, 7, 2),
(290, 3, 2),
(291, 2, 2),
(292, 1, 2),
(293, 2, 2),
(294, 3, 2),
(295, 7, 2),
(296, 6, 2),
(297, 9, 2),
(298, 8, 2),
(299, 9, 2),
(300, 6, 2),
(301, 7, 2),
(302, 3, 2),
(303, 2, 2),
(304, 1, 2),
(305, 2, 2),
(306, 3, 2),
(307, 7, 2),
(308, 6, 2),
(309, 9, 2),
(310, 8, 2),
(311, 9, 2),
(312, 6, 2),
(313, 7, 2),
(314, 3, 2),
(315, 2, 2),
(316, 1, 2),
(317, 2, 2),
(318, 3, 2),
(319, 7, 2),
(320, 6, 2),
(321, 9, 2),
(322, 9, 2),
(323, 6, 2),
(324, 8, 2),
(325, 7, 2),
(326, 3, 2),
(327, 2, 2),
(328, 1, 2),
(329, 2, 2),
(330, 3, 2),
(331, 7, 2),
(332, 6, 2),
(333, 9, 2),
(334, 8, 2),
(335, 9, 2),
(336, 6, 2),
(337, 7, 2),
(338, 3, 2),
(339, 2, 2),
(340, 1, 2),
(341, 2, 2),
(342, 3, 2),
(343, 7, 2),
(344, 6, 2),
(345, 9, 2),
(346, 8, 2),
(347, 9, 2),
(348, 6, 2),
(349, 7, 2),
(350, 3, 2),
(351, 2, 2),
(352, 1, 2),
(353, 2, 2),
(354, 3, 2),
(355, 7, 2),
(356, 6, 2),
(357, 9, 2),
(358, 8, 2),
(359, 1, 2),
(360, 1, 2),
(361, 1, 2),
(362, 1, 2),
(363, 1, 2),
(364, 1, 2),
(365, 1, 2),
(366, 1, 2),
(367, 1, 2),
(368, 1, 2),
(369, 1, 2),
(370, 1, 2),
(371, 1, 2),
(372, 1, 2),
(373, 1, 2),
(374, 1, 2),
(375, 1, 2),
(376, 1, 2),
(377, 1, 2),
(378, 1, 2),
(379, 1, 2),
(380, 1, 2),
(381, 1, 2),
(382, 1, 2),
(383, 1, 2),
(384, 1, 2),
(385, 2, 2),
(386, 3, 2),
(387, 7, 2),
(388, 6, 2),
(389, 9, 2),
(390, 8, 2),
(391, 9, 2),
(392, 6, 2),
(393, 7, 2),
(394, 3, 2),
(395, 2, 2),
(396, 1, 2),
(397, 1, 2),
(398, 1, 2),
(399, 1, 2),
(400, 1, 2),
(401, 1, 2),
(402, 4, 2),
(403, 4, 2),
(404, 4, 2),
(405, 4, 2),
(406, 1, 2),
(407, 1, 2),
(408, 1, 2),
(409, 1, 2),
(410, 1, 2),
(411, 1, 2),
(412, 1, 2),
(413, 1, 2),
(414, 1, 2),
(415, 1, 4),
(416, 1, 4),
(417, 1, 4),
(418, 1, 2),
(419, 1, 2),
(420, 1, 2),
(421, 1, 2),
(422, 1, 2),
(423, 1, 2),
(424, 2, 2),
(425, 3, 2),
(426, 6, 2),
(427, 3, 2),
(428, 2, 2),
(429, 1, 2),
(430, 1, 2),
(431, 1, 2),
(432, 1, 2),
(433, 2, 2),
(434, 3, 2),
(435, 2, 2),
(436, 1, 2),
(437, 1, 2),
(438, 1, 2),
(439, 1, 2),
(440, 1, 2),
(441, 1, 2),
(442, 7, 2),
(443, 6, 2),
(444, 3, 2),
(445, 1, 2),
(446, 1, 2),
(447, 1, 2),
(448, 1, 2),
(449, 1, 2),
(450, 1, 2),
(451, 1, 2),
(452, 1, 2),
(453, 1, 2),
(454, 1, 2),
(455, 1, 2),
(456, 1, 2),
(457, 1, 2),
(458, 1, 2),
(459, 1, 2),
(460, 1, 2),
(461, 1, 2),
(462, 1, 2),
(463, 1, 2),
(464, 2, 2),
(465, 3, 2),
(466, 6, 2),
(467, 7, 2),
(468, 2, 2),
(469, 7, 2),
(470, 6, 2),
(471, 3, 2),
(472, 1, 2),
(473, 1, 2),
(474, 1, 2),
(475, 1, 2),
(476, 1, 2),
(477, 1, 3),
(478, 1, 2),
(479, 1, 2),
(480, 1, 2),
(481, 1, 2),
(482, 1, 2),
(483, 1, 2),
(484, 1, 2),
(485, 1, 2),
(486, 1, 2),
(487, 1, 2),
(488, 1, 2),
(489, 1, 2),
(490, 1, 2),
(491, 1, 2),
(492, 1, 2),
(493, 1, 2),
(494, 1, 2),
(495, 1, 2),
(496, 1, 2),
(497, 1, 2),
(498, 1, 2),
(499, 1, 2),
(500, 1, 2),
(501, 1, 2),
(502, 1, 2);

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
(5, 2, 2, 'Segundo', 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. ', NULL, '', 'active', '2010-09-27 19:47:44'),
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
  `status` enum('active','inactive','final') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `exercise`
--

INSERT INTO `exercise` (`id`, `user_id`, `classroom_id`, `name`, `time`, `begin`, `end`, `attempts`, `status`) VALUES
(1, 2, 7, 'Primeiro', 0, '2010-10-20', '2010-11-23', 1, 'final'),
(2, 2, 6, 'Outro', NULL, '2010-10-21', NULL, 2, 'active'),
(3, 2, 7, 'Segundo', 10, '2010-10-26', '2010-11-06', 0, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_answer`
--

CREATE TABLE IF NOT EXISTS `exercise_answer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `exercise_option_id` bigint(20) NOT NULL,
  `exercise_note_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_value_id` (`exercise_option_id`),
  KEY `exercise_note_id` (`exercise_note_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `exercise_answer`
--

INSERT INTO `exercise_answer` (`id`, `exercise_option_id`, `exercise_note_id`) VALUES
(14, 8, 14),
(15, 13, 14),
(16, 20, 14),
(17, 9, 15),
(18, 13, 15),
(19, 18, 15),
(22, 23, 23);

-- --------------------------------------------------------

--
-- Table structure for table `exercise_note`
--

CREATE TABLE IF NOT EXISTS `exercise_note` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `exercise_id` bigint(20) NOT NULL,
  `note` tinyint(3) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `exercise_id` (`exercise_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `exercise_note`
--

INSERT INTO `exercise_note` (`id`, `user_id`, `exercise_id`, `note`, `created`) VALUES
(14, 2, 1, 100, '2010-11-04 22:53:41'),
(15, 2, 1, 33, '2010-11-04 23:53:55'),
(23, 2, 3, 33, '2010-11-07 00:53:57'),
(26, 2, 3, 0, '2010-11-07 01:05:16'),
(27, 2, 3, 0, '2010-11-07 01:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_option`
--

CREATE TABLE IF NOT EXISTS `exercise_option` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `exercise_question_id` bigint(20) NOT NULL,
  `description` text NOT NULL,
  `justify` text,
  `status` enum('right','wrong') NOT NULL DEFAULT 'wrong',
  PRIMARY KEY (`id`),
  KEY `exercise_question_id` (`exercise_question_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `exercise_option`
--

INSERT INTO `exercise_option` (`id`, `exercise_question_id`, `description`, `justify`, `status`) VALUES
(1, 1, 'Opcao 1', NULL, 'right'),
(3, 1, 'Opcao 2', NULL, 'wrong'),
(4, 1, 'Opcao 3', NULL, 'wrong'),
(5, 1, 'Opcao 4', NULL, 'wrong'),
(8, 3, 'Opcao 1', NULL, 'right'),
(9, 3, 'Opcao 2', NULL, 'wrong'),
(10, 3, 'Opcao 3', NULL, 'wrong'),
(11, 3, 'Opcao 4', NULL, 'wrong'),
(12, 4, 'Opcao 2.1', NULL, 'wrong'),
(13, 4, 'Opcao 2.2', NULL, 'right'),
(14, 4, 'Opcao 2.3.1', NULL, 'wrong'),
(18, 6, 'Opcao 3.1', NULL, 'wrong'),
(19, 6, 'Opcao 3.2', NULL, 'wrong'),
(20, 6, 'Opcao 3.3', NULL, 'right'),
(21, 7, 'Opcao 4.1', NULL, 'wrong'),
(22, 8, 'Opcao 4.1', NULL, 'right'),
(23, 9, 'Opcao 1', NULL, 'right'),
(24, 9, 'Opcao 2', NULL, 'wrong'),
(25, 9, 'Opcao 3', NULL, 'wrong'),
(26, 9, 'Opcao 4', NULL, 'wrong'),
(27, 10, 'Opcao 4.1', NULL, 'right'),
(28, 11, 'Opcao 3.1', NULL, 'wrong'),
(29, 11, 'Opcao 3.2', NULL, 'wrong'),
(30, 11, 'Opcao 3.3', NULL, 'right');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_question`
--

CREATE TABLE IF NOT EXISTS `exercise_question` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `exercise_id` bigint(20) DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `description` text NOT NULL,
  `note` tinyint(3) DEFAULT NULL,
  `position` int(10) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `exercise_id` (`exercise_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `exercise_question`
--

INSERT INTO `exercise_question` (`id`, `exercise_id`, `parent_id`, `description`, `note`, `position`, `status`) VALUES
(1, NULL, NULL, 'Questao 1', 33, NULL, 'active'),
(3, 1, 1, 'Questao 1', 33, 0, 'active'),
(4, 1, NULL, 'Questao 2', 33, 1, 'active'),
(6, 1, NULL, 'Questao 3', 34, 2, 'active'),
(7, NULL, NULL, 'Questao 4', 0, NULL, 'active'),
(8, NULL, 7, 'Questao 4', 0, 0, 'inactive'),
(9, 3, 3, 'Questao 1', 33, 0, 'active'),
(10, NULL, 8, 'Questao 4', 0, 2, 'inactive'),
(11, 3, NULL, 'Questao 3', 34, 1, 'active');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `user_id`, `classroom_id`, `question`, `answer`) VALUES
(1, 2, 3, 'pq? pqqqq??', 'uai, pq sim! funfa'),
(2, 2, 3, 'outra pergunta veihhhhhh?', 'serio! outra resposta, hehehhe!'),
(4, 2, 5, 'Ainda tenho alguns questionamentos...', 'é mesmo?!'),
(5, 2, 7, 'Curso - ,.f amsdfm', 'k jadsfja,gsdfg,asgdmf');

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
(2, 3, 3, 'poaus dpfoiu paosdfa', '4c9cf52468077', '2010-09-24 15:59:48'),
(3, 2, 3, 'eqwryoq qoiwer oiquew', '4c9cf54409176', '2010-09-24 16:00:20'),
(4, 2, 7, 'sadf', '4cd61c588098b', '2010-11-07 01:26:16');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `forum_reply`
--

INSERT INTO `forum_reply` (`id`, `forum_id`, `user_id`, `description`, `created`) VALUES
(6, 1, 2, 'akljdsf lkjfajdfklalkdfhalkjdshflka', '2010-09-24 23:46:37'),
(9, 1, 2, 'kljas fkjlaslkfjasdf\n\n\nasdflk asdfjalkdflkahdsf\n\nasdfja sdfhakljdflkjasdf', '2010-09-25 00:10:17'),
(13, 4, 3, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\n\n\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '2010-09-28 23:29:32'),
(15, 4, 4, 'asdnf kljashdfkja sdfhjkasdf eqrqwerqwerqer', '2010-09-30 23:48:34');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `glossary`
--

INSERT INTO `glossary` (`id`, `user_id`, `classroom_id`, `word`, `description`, `created`) VALUES
(2, 2, 3, 'novo alter asdf', 'asjlkdfh kajlsdflkjad kfljasdf askdfhj kalsdhf kljahds lkjhads klfjasd\nf\nasdfkl;a sdlkf akljsdf\n\nasdlkjfh alkjsdfkaasd.,fa sdflkadsf\nasdlkjf klasjdhf lkja dsklfakljsdhklajsdfjk', '2010-09-22 17:45:58'),
(3, 2, 3, 'qwerqwerq wer qwe rqe', 'asdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhjasdlkjf alsdfhj', '2010-09-22 17:58:12'),
(4, 2, 3, 'caracas', 'sdfla;sd fadsfljalkdfkadhjfs', '2010-09-22 18:32:02'),
(5, 2, 7, 'casa', 'legal a descricao', '2010-10-07 22:02:19'),
(6, 2, 7, 'uai', 'asdf;knaskjdbfa', '2010-10-07 22:03:32'),
(8, 2, 7, 'agora sim', 'asdjkfakjl fdakldfa', '2010-10-07 22:05:01'),
(9, 4, 7, 'mais um', 'aslkdjfh alskjfkajdslfkjakdsfa', '2010-10-07 22:24:39');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `sender`, `receiver`, `description`, `created`) VALUES
(2, 2, 2, 'oi pfuqwoieruqiopuweropiqpoeroqpe', '2010-09-28 23:44:42'),
(3, 2, 4, 'blablabaaablaal aaaaa', '2010-09-29 00:04:24'),
(4, 2, 4, 'caracas aaa', '2010-09-29 00:04:55'),
(5, 4, 4, 'opa valeu!', '2010-09-29 00:08:30'),
(7, 3, 4, 'fhufhsdjfhjsdhgj kjnfdsnfjks', '2010-09-30 23:27:33'),
(9, 4, 3, 's;akdfj afjaldfkja dkfajfakjlhfkladfjakldsf', '2010-09-30 23:43:05'),
(10, 4, 3, 'asdfha fkjladfkljalkdsfklajdfkjakdjf klajdf kjadkjlfa kljf kljah dklfja kdfakdfajdf akljsd hkasdfha fkjladfkljalkdsfklajdfkjakdjf klajdf kjadkjlfa kljf kljah dklfja kdfakdfajdf akljsd hkasdfha fkjladfkljalkdsfklajdfkjakdjf klajdf kjadkjlfa kljf kljah dklfja kdfakdfajdf akljsd hkasdfha fkjladfkljalkdsfklajdfkjakdjf klajdf kjadkjlfa kljf kljah dklfja kdfakdfajdf akljsd hk\n\nasdfha fkjladfkljalkdsfklajdfkjakdjf klajdf kjadkjlfa kljf kljah dklfja kdfakdfajdf akljsd hkasdfha fkjladfkljalkdsfklajdfkjakdjf klajdf kjadkjlfa kljf kljah dklfja kdfakdfajdf akljsd hkasdfha fkjladfkljalkdsfklajdfkjakdjf klajdf kjadkjlfa kljf kljah dklfja kdfakdfajdf akljsd hkasdfha fkjladfkljalkdsfklajdfkjakdjf klajdf kjadkjlfa kljf kljah dklfja kdfakdfajdf akljsd hk', '2010-09-30 23:43:11'),
(11, 2, 2, 'asf asdf a sdf as df a sdfsa', '2010-11-07 01:29:21');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `notepad`
--

INSERT INTO `notepad` (`id`, `classroom_id`, `user_id`, `description`, `created`) VALUES
(9, 3, 2, 'asfdjn akjlsfljasdfkakldshalkdsfasdf klajsdfkjlahds kljfa kldsf kaldsf', '2010-09-23 09:12:56'),
(10, 3, 2, 'asdf', '2010-09-23 09:12:59'),
(11, 3, 2, 'qwer', '2010-09-23 09:13:02'),
(12, 3, 2, 'ZXvzxcv', '2010-09-23 09:13:05'),
(14, 7, 2, 'oiiiiieeeee', '2010-09-28 23:42:00'),
(15, 7, 2, 'oipadsfj apidfadjfapdiufaisdfiausdfpiaudsf', '2010-09-28 23:43:05'),
(16, 7, 2, 'sdfghjkl,;.''', '2010-09-28 23:44:02'),
(17, 7, 2, 'asdfasdfa', '2010-10-18 18:39:17'),
(18, 7, 2, 'asdf a sdf asdf', '2010-10-18 18:39:57'),
(19, 7, 2, 'asdf asd f asd fa', '2010-11-06 23:49:12');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `panel`
--

INSERT INTO `panel` (`id`, `classroom_id`, `type`, `item_id`) VALUES
(1, 7, 'activity', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `panel_note`
--

INSERT INTO `panel_note` (`id`, `user_id`, `panel_id`, `note`) VALUES
(1, 2, 1, '8.00');

-- --------------------------------------------------------

--
-- Table structure for table `restriction_panel`
--

CREATE TABLE IF NOT EXISTS `restriction_panel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `classroom_id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `note` decimal(10,2) DEFAULT NULL,
  `panel_id` int(20) NOT NULL,
  `note_restriction` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `restriction_panel`
--

INSERT INTO `restriction_panel` (`id`, `classroom_id`, `content_id`, `note`, `panel_id`, `note_restriction`) VALUES
(1, 7, 2, '5.00', 1, '7.00');

-- --------------------------------------------------------

--
-- Table structure for table `restriction_time`
--

CREATE TABLE IF NOT EXISTS `restriction_time` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `classroom_id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `begin` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `restriction_time`
--


-- --------------------------------------------------------

--
-- Table structure for table `timeline`
--

CREATE TABLE IF NOT EXISTS `timeline` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `classroom_id` bigint(20) NOT NULL,
  `description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `classroom_id` (`classroom_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `timeline`
--

INSERT INTO `timeline` (`id`, `user_id`, `classroom_id`, `description`, `created`) VALUES
(1, 2, 7, 'alguem fez algo', '2010-10-01 00:29:08'),
(2, 2, 7, 'saved new word in glossary - agora sim', '2010-10-07 22:05:01'),
(3, 2, 5, 'created a new FAQ - Ainda tenho alguns questionamentos...', '2010-10-07 22:18:37'),
(4, 4, 7, 'saved a new word in the glossary - mais um', '2010-10-07 22:24:39'),
(5, 2, 7, 'criou uma nova atividade - caracas', '2010-10-18 19:49:37'),
(6, 2, 7, 'created a new exercise', '2010-10-26 19:55:25'),
(7, 2, 7, 'criou uma nova FAQ - ,.f amsdfm', '2010-11-03 14:14:06'),
(8, 2, 7, 'criou uma nova atividade - asdfadsf', '2010-11-03 14:17:17'),
(9, 2, 7, 'adicionou um novo arquivo - sadf', '2010-11-07 01:26:16');

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
  ADD CONSTRAINT `activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `activity_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `activity_text`
--
ALTER TABLE `activity_text`
  ADD CONSTRAINT `activity_text_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `activity_text_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `activity_text_ibfk_3` FOREIGN KEY (`sender`) REFERENCES `user` (`id`);

--
-- Constraints for table `calendar`
--
ALTER TABLE `calendar`
  ADD CONSTRAINT `calendar_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `calendar_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `calendar_ibfk_3` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `certificate`
--
ALTER TABLE `certificate`
  ADD CONSTRAINT `certificate_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `certificate_user`
--
ALTER TABLE `certificate_user`
  ADD CONSTRAINT `certificate_user_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`id`),
  ADD CONSTRAINT `certificate_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `user` (`id`);

--
-- Constraints for table `chat_room`
--
ALTER TABLE `chat_room`
  ADD CONSTRAINT `chat_room_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `chat_room_message`
--
ALTER TABLE `chat_room_message`
  ADD CONSTRAINT `chat_room_message_ibfk_1` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_room` (`id`),
  ADD CONSTRAINT `chat_room_message_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `classroom`
--
ALTER TABLE `classroom`
  ADD CONSTRAINT `classroom_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  ADD CONSTRAINT `classroom_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  ADD CONSTRAINT `classroom_ibfk_3` FOREIGN KEY (`responsible`) REFERENCES `user` (`id`);

--
-- Constraints for table `classroom_user`
--
ALTER TABLE `classroom_user`
  ADD CONSTRAINT `classroom_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `classroom_user_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `configuration`
--
ALTER TABLE `configuration`
  ADD CONSTRAINT `configuration_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  ADD CONSTRAINT `content_ibfk_2` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`);

--
-- Constraints for table `content_access`
--
ALTER TABLE `content_access`
  ADD CONSTRAINT `content_access_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`),
  ADD CONSTRAINT `content_access_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`responsible`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `course_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `exercise`
--
ALTER TABLE `exercise`
  ADD CONSTRAINT `exercise_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `exercise_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `exercise_answer`
--
ALTER TABLE `exercise_answer`
  ADD CONSTRAINT `exercise_answer_ibfk_2` FOREIGN KEY (`exercise_option_id`) REFERENCES `exercise_option` (`id`),
  ADD CONSTRAINT `exercise_answer_ibfk_3` FOREIGN KEY (`exercise_note_id`) REFERENCES `exercise_note` (`id`);

--
-- Constraints for table `exercise_option`
--
ALTER TABLE `exercise_option`
  ADD CONSTRAINT `exercise_option_ibfk_1` FOREIGN KEY (`exercise_question_id`) REFERENCES `exercise_question` (`id`);

--
-- Constraints for table `exercise_question`
--
ALTER TABLE `exercise_question`
  ADD CONSTRAINT `exercise_question_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`id`);

--
-- Constraints for table `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `faq_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `file_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `forum_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `forum_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `forum_reply`
--
ALTER TABLE `forum_reply`
  ADD CONSTRAINT `forum_reply_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`id`),
  ADD CONSTRAINT `forum_reply_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `glossary`
--
ALTER TABLE `glossary`
  ADD CONSTRAINT `glossary_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `glossary_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `glossary_ibfk_3` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `log_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `user` (`id`);

--
-- Constraints for table `notepad`
--
ALTER TABLE `notepad`
  ADD CONSTRAINT `notepad_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`),
  ADD CONSTRAINT `notepad_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `panel`
--
ALTER TABLE `panel`
  ADD CONSTRAINT `panel_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);

--
-- Constraints for table `panel_note`
--
ALTER TABLE `panel_note`
  ADD CONSTRAINT `panel_note_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `panel_note_ibfk_2` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`id`);

--
-- Constraints for table `timeline`
--
ALTER TABLE `timeline`
  ADD CONSTRAINT `timeline_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `timeline_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`);
