-- phpMyAdmin SQL Dump
-- version 4.4.0
-- http://www.phpmyadmin.net
--
-- Host: 172.31.19.99
-- Generation Time: May 29, 2015 at 11:23 AM
-- Server version: 5.6.14-log
-- PHP Version: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `technical`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `cid` int(11) NOT NULL COMMENT 'Category ID',
  `name` varchar(60) NOT NULL COMMENT 'Category Name'
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cid`, `name`) VALUES
(3, 'Contributed Modules'),
(65, 'Custom Modules'),
(69, 'General Development'),
(68, 'Management'),
(67, 'Networking'),
(66, 'Website Forms');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(11) NOT NULL COMMENT 'Auto-increment ID',
  `title` varchar(255) NOT NULL COMMENT 'The title of the document',
  `content` longtext NOT NULL COMMENT 'The content of the document',
  `category` mediumtext NOT NULL COMMENT 'The category or categories of the document',
  `status` tinyint(1) NOT NULL COMMENT 'The status of the document. Published = 1, Draft = 2',
  `created_at` datetime NOT NULL COMMENT 'The creation Date',
  `updated_at` datetime DEFAULT NULL COMMENT 'The updated date',
  `created_by` varchar(60) NOT NULL,
  `updated_by` varchar(60) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `title`, `content`, `category`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Software Development', '<p><strong>Software develop</strong><img alt="" src="/images/uploads/webespire_software_development.jpg" style="float:right; height:300px; line-height:1.6; margin:4px; width:300px" /><strong>ment</strong>&nbsp;is the&nbsp;<a href="http://en.wikipedia.org/wiki/Computer_programming" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Computer programming">computer programming</a>,&nbsp;<a href="http://en.wikipedia.org/wiki/Software_documentation" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software documentation">documenting</a>,&nbsp;<a href="http://en.wikipedia.org/wiki/Software_testing" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software testing">testing</a>, and&nbsp;<a class="mw-redirect" href="http://en.wikipedia.org/wiki/Software_bugs" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software bugs">bug fixing</a>&nbsp;involved in creating and maintaining&nbsp;<a href="http://en.wikipedia.org/wiki/Application_software" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Application software">applications</a>&nbsp;and<a href="http://en.wikipedia.org/wiki/Software_framework" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software framework">frameworks</a>&nbsp;involved in a&nbsp;<a href="http://en.wikipedia.org/wiki/Software_release_life_cycle" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software release life cycle">software release life cycle</a>&nbsp;and resulting in a&nbsp;<a class="mw-redirect" href="http://en.wikipedia.org/wiki/Software_product" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software product">software product</a>. The term refers to a process of writing and&nbsp;<a href="http://en.wikipedia.org/wiki/Software_maintenance" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software maintenance">maintaining</a>&nbsp;the&nbsp;<a href="http://en.wikipedia.org/wiki/Source_code" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Source code">source code</a>, but in a broader sense of the term it includes all that is involved between the conception of the desired software through to the final manifestation of the software, ideally in a planned and&nbsp;<a href="http://en.wikipedia.org/wiki/Software_development_process" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software development process">structured</a>&nbsp;process.<a href="http://en.wikipedia.org/wiki/Software_development#cite_note-1" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); white-space: nowrap; background: none;">[1]</a>&nbsp;Therefore, software development may include research, new development, prototyping, modification, reuse, re-engineering, maintenance, or any other activities that result in software products.<a href="http://en.wikipedia.org/wiki/Software_development#cite_note-2" style="line-height: 1.6; text-decoration: none; color: rgb(11, 0, 128); white-space: nowrap; background: none;">[2]</a></p>\n\n<p>&nbsp;</p>\n\n<p>&nbsp;</p>\n\n<p>Software can be developed for a variety of purposes, the three most common being to meet specific needs of a specific client/business (the case with<a href="http://en.wikipedia.org/wiki/Custom_software" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Custom software">custom software</a>), to meet a perceived need of some set of potential&nbsp;<a href="http://en.wikipedia.org/wiki/User_(computing)" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="User (computing)">users</a>&nbsp;(the case with&nbsp;<a href="http://en.wikipedia.org/wiki/Commercial_software" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Commercial software">commercial</a>&nbsp;and&nbsp;<a class="mw-redirect" href="http://en.wikipedia.org/wiki/Open_source_software" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Open source software">open source software</a>), or for personal use (e.g. a scientist may write software to automate a mundane task).&nbsp;<strong>Embedded software development</strong>, that is, the development of&nbsp;<a href="http://en.wikipedia.org/wiki/Embedded_software" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Embedded software">embedded software</a>&nbsp;such as used for controlling consumer products, requires the development process to be integrated with the development of the controlled physical product.&nbsp;<a href="http://en.wikipedia.org/wiki/System_software" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="System software">System software</a>&nbsp;underlies applications and the programming process itself, and is often developed separately.</p>\n\n<p>The need for better&nbsp;<a href="http://en.wikipedia.org/wiki/Quality_control" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Quality control">quality control</a>&nbsp;of the software development process has given rise to the discipline of&nbsp;<a href="http://en.wikipedia.org/wiki/Software_engineering" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Software engineering">software engineering</a>, which aims to apply the systematic approach exemplified in the&nbsp;<a href="http://en.wikipedia.org/wiki/Engineering" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Engineering">engineering</a>&nbsp;paradigm to the process of software development.</p>\n\n<p>There are many approaches to software project management, known as software development life cycle models, methodologies, processes, or models. The<a href="http://en.wikipedia.org/wiki/Waterfall_model" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Waterfall model">waterfall model</a>&nbsp;is a traditional version, contrasted with the more recent innovation of&nbsp;<a href="http://en.wikipedia.org/wiki/Agile_software_development" style="text-decoration: none; color: rgb(11, 0, 128); background: none;" title="Agile software development">agile software development</a>.</p>\n', 'a:1:{s:19:"general-development";s:19:"General Development";}', 1, '2015-05-06 21:35:43', '2015-05-18 14:01:07', 'jgarcia', 'jgarcia'),
(2, 'Networking Systems', '<h2><strong>Lorem ipsum</strong></h2>\r\n\r\n<p><strong>Lorem ipsum dolor sit amet</strong>, consectetur adipiscing elit. Nam dapibus, ligula vel tempor accumsan, dolor dui suscipit mauris, eu fringilla velit lorem eget nisi. Suspendisse porttitor eros ut risus eleifend accumsan. Vestibulum condimentum nibh augue, ac euismod mi feugiat eget. Quisque ut tortor a ante dapibus scelerisque. Curabitur quis leo nisi. Integer mi tellus, lobortis efficitur lacus ac, fermentum pellentesque neque.</p>\r\n\r\n<p><img alt="" src="http://zaininfotech.com/wp-content/uploads/2014/12/rj45_networking_cables_bokeh-1920x600.jpg" style="height:188px; width:600px" /></p>\r\n\r\n<p>Curabitur nec eros tempor lorem fringilla interdum eu ut enim. Sed ultricies risus nec eros imperdiet lacinia. Sed feugiat sem et sem blandit pharetra. Cras pellentesque, libero vitae vestibulum tempus, quam lectus pellentesque ante, vitae venenatis erat nisl sed quam. Suspendisse pharetra congue metus, in rhoncus massa gravida blandit. Cras at ligula risus. Ut non luctus massa. Vestibulum ultricies sagittis libero, sit amet euismod lectus pulvinar vel.</p>\r\n', 'a:1:{s:10:"networking";s:10:"Networking";}', 1, '2015-05-07 15:00:49', '2015-05-18 13:56:21', 'jgarcia', 'jgarcia'),
(3, 'Management & Leadership', '<h2><strong>Lorem ipsum dolor</strong></h2>\r\n\r\n<p>Sit amet, consectetur adipiscing elit. Sed quis massa et dui tincidunt varius vel quis felis. Curabitur ante est, volutpat a velit nec, convallis fringilla nibh. Suspendisse a semper quam. Vivamus eu suscipit dolor, id efficitur <em>ipsum</em>. Vivamus arcu lorem, malesuada non sodales id, viverra vitae lectus. In arcu justo, volutpat at ligula sit amet, semper fringilla libero. Quisque iaculis vitae erat eu suscipit. Sed aliquam a magna eu laoreet. Suspendisse tempus ut lorem sit amet congue. Quisque viverra neque accumsan quam efficitur, pharetra sollicitudin risus vestibulum. Nam pretium nibh nec purus dapibus, at viverra sapien feugiat. In dapibus lacus nec mauris vulputate, et commodo nulla tincidunt. Duis eget leo mi.</p>\r\n\r\n<p><img alt="" src="/images/uploads/shutterstock.jpg" style="float:left; height:313px; margin:4px 10px; width:500px" /></p>\r\n\r\n<p>Nunc fringilla eget orci vitae fringilla. Sed condimentum, dui sit amet ultricies aliquet, libero elit rutrum metus, a iaculis dui augue a sem. Sed tincidunt placerat nulla tempor elementum. Curabitur rhoncus leo lorem, id scelerisque erat convallis id. Sed sed nibh ac odio varius pulvinar. Proin id sollicitudin arcu, et sodales orci. Ut suscipit, lacus id semper accumsan, nulla libero egestas erat, ut dignissim felis dolor sit amet urna. Cras bibendum velit eget tortor consequat viverra. Donec magna orci, rhoncus accumsan nisi nec, mattis semper sapien. Etiam non vestibulum turpis, vel tempor est. Nullam molestie a turpis in maximus. Mauris dignissim dapibus maximus. Mauris maximus libero nisl, feugiat faucibus nibh consequat vel. Vestibulum porttitor a sapien ac rutrum.</p>\r\n\r\n<p>Nulla finibus tincidunt accumsan. Suspendisse potenti. Integer consectetur, metus non gravida convallis, diam turpis porta nisl, ac sagittis est urna pellentesque nulla. Sed tristique urna id vehicula congue. Nulla id porta ante. Praesent vitae odio neque. Ut mi quam, aliquet non aliquet eu, efficitur non eros. Suspendisse vulputate finibus quam sed tempus. Cras ut libero ac tellus vulputate fermentum. Nunc at eleifend metus, nec pellentesque leo. Nulla scelerisque neque nec est viverra porta. Nam cursus augue sit amet nibh pretium, venenatis viverra purus vehicula. Quisque nec suscipit ligula, a aliquam ipsum. Fusce fermentum nibh enim, eu rutrum massa gravida vitae.</p>\r\n', 'a:1:{s:10:"management";s:10:"Management";}', 1, '2015-05-08 19:29:11', '2015-05-13 18:30:51', 'gtheisen', 'jgarcia');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL COMMENT 'User unique auto increment ID',
  `username` varchar(64) NOT NULL COMMENT 'User Unique User Name',
  `password` varchar(255) NOT NULL COMMENT 'User''s password',
  `fname` varchar(64) NOT NULL COMMENT 'User''s First Name',
  `lname` varchar(64) NOT NULL COMMENT 'User''s Last Name',
  `email` varchar(64) NOT NULL COMMENT 'User Unique User email',
  `role` tinyint(1) NOT NULL COMMENT 'User''s role. 1 = admin, 2 = Content Creator',
  `login_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `fname`, `lname`, `email`, `role`, `login_token`) VALUES
(3, 'jgarcia', '$2y$10$mMQDH23rfOVn6DyX7jkRhunMDkSxGOc8h/GdbgP84snJCETENWacm', 'Jon', 'Garcia', 'cuna@me.com', 1, '676f5b5c31bf3c82a24d6bc3578bef43e3e6da41708c0758df041bf47a41ace6'),
(4, 'admin', '$2y$10$bmFmRTFVEi.JBqKcxjfgGeJNrouEHHiyQrYYsvDWBONqB/cZ4cmFO', 'Helpdesk', 'Helpdesk', 'helpdesk@mercy.edu', 1, ''),
(6, 'gtheisen', '$2y$10$PTdiHj9f7Hz7v7tkCw8G7.evKQKUHIfjHrEXuO/KV1disRITEgY66', 'Greg', 'Theisen', 'gtheisen@mercy.edu', 1, ''),
(7, 'juser', '$2y$10$QCE8v39JKHuAXEpWxQVQBe6Df3Dh/SNhK05FTjRr8UPzYVgrxYxPK', 'J', 'User', 'juser@mercy.non', 2, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Category ID',AUTO_INCREMENT=70;
--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Auto-increment ID',AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User unique auto increment ID',AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
