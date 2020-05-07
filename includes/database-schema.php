<?php

if (!defined('SECURE_BOOT')) exit();

$schema = [];

$schema[] = "
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `specialization` int(11) NOT NULL,
  `degree` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `doctors_users` (`user`),
  KEY `doctors_specializations` (`specialization`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$schema[] = "
CREATE TABLE IF NOT EXISTS `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `pesel` int(11) NOT NULL,
  `phone` int(9) NOT NULL,
  `street` varchar(30) CHARACTER SET utf8 NOT NULL,
  `house_no` varchar(10) CHARACTER SET utf8 NOT NULL,
  `city` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patients_users` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$schema[] = "
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `patient` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  KEY `reservation_date` (`date`),
  KEY `reservation_patient` (`patient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$schema[] = "
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$schema[] = "
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(10) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$schema[] = "
CREATE TABLE IF NOT EXISTS `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `schedule_doctor` (`doctor`),
  KEY `schedule_rooms` (`room`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$schema[] = "
CREATE TABLE IF NOT EXISTS `specializations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$schema[] = "
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `password` varchar(72) CHARACTER SET utf8 NOT NULL,
  `firstname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `lastname` varchar(25) CHARACTER SET utf8 NOT NULL,
  `role` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_roles` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$schema[] = "
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_specializations` FOREIGN KEY (`specialization`) REFERENCES `specializations` (`id`),
  ADD CONSTRAINT `doctors_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;";

$schema[] = "
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;";

$schema[] = "
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservation_date` FOREIGN KEY (`date`) REFERENCES `schedule` (`id`),
  ADD CONSTRAINT `reservation_patient` FOREIGN KEY (`patient`) REFERENCES `patients` (`id`);";

$schema[] = "
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_doctor` FOREIGN KEY (`doctor`) REFERENCES `doctors` (`id`),
  ADD CONSTRAINT `schedule_rooms` FOREIGN KEY (`room`) REFERENCES `rooms` (`id`);";

$schema[] = "
ALTER TABLE `users`
  ADD CONSTRAINT `users_roles` FOREIGN KEY (`role`) REFERENCES `roles` (`id`);";

$schema[] = "
INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Administrator', 'ADMIN'),
(2, 'Pracownik', 'EMPLOYEE'),
(3, 'Lekarz', 'DOCTOR'),
(4, 'Pacjent', 'PATIENT');";

return $schema;
