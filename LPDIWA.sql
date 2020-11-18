CREATE TABLE `classe` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `titre` varchar(255),
  `position` int,
  `niveau` varchar(255),
  `createdAt` datetime,
  `updatedAt` datetime,
  `description` varchar(255),
  `vignetteName` varchar(255),
  `isVisible` tinyint
);

CREATE TABLE `enseignants_classes` (
  `enseignant_id` int PRIMARY KEY AUTO_INCREMENT,
  `classe_id` int
);

CREATE TABLE `enseignant` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `identite` varchar(255),
  `createdAt` datetime,
  `updatedAt` datetime,
  `vignetteName` varchar(255),
  `isVisible` tinyint
);

CREATE TABLE `articleecole` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `classe_id` int,
  `titre` longtext,
  `description` longtext,
  `createdAt` datetime,
  `updated` datetime,
  `visible` tinyint,
  `vignetteName` varchar(255)
);

CREATE TABLE `user` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `password` varchar(255),
  `email` varchar(255),
  `roles` longtext
);

ALTER TABLE `classe` ADD FOREIGN KEY (`id`) REFERENCES `enseignants_classes` (`classe_id`);

ALTER TABLE `classe` ADD FOREIGN KEY (`id`) REFERENCES `articleecole` (`classe_id`);

ALTER TABLE `enseignants_classes` ADD FOREIGN KEY (`enseignant_id`) REFERENCES `enseignant` (`id`);
