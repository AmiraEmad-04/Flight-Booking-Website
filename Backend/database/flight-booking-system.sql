-- ==============================
-- DROP & CREATE DATABASE
-- ==============================

DROP DATABASE IF EXISTS `flight-booking-system`;
CREATE DATABASE IF NOT EXISTS `flight-booking-system`
CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;

USE `flight-booking-system`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ==============================
-- USERS
-- ==============================

CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `tel` VARCHAR(20) NOT NULL,
  `type` ENUM('company','passenger') NOT NULL,
  `account_balance` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB;

-- ==============================
-- COMPANIES
-- ==============================

CREATE TABLE `companies` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `bio` TEXT,
  `address` VARCHAR(500),
  `location` VARCHAR(255),
  `logo_img` VARCHAR(255),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `companies_fk_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- PASSENGERS
-- ==============================

-- CREATE TABLE `passengers` (
--   `id` INT(11) NOT NULL AUTO_INCREMENT,
--   `user_id` INT(11) NOT NULL,
--   `photo` VARCHAR(255),
--   `passport_img` VARCHAR(255),
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `user_id` (`user_id`),
--   CONSTRAINT `passengers_fk_user`
--     FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
--     ON DELETE CASCADE
-- ) ENGINE=InnoDB;
CREATE TABLE passengers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tel VARCHAR(20),
    photo VARCHAR(255),
    passport_img VARCHAR(255),
    account_balance DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE passenger_flights (
    id INT PRIMARY KEY AUTO_INCREMENT,
    passenger_id INT,
    flight_id INT,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    payment_type ENUM('account', 'cash'),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (passenger_id) REFERENCES passengers(id),
    FOREIGN KEY (flight_id) REFERENCES flights(id)
);

-- ==============================
-- FLIGHTS
-- ==============================

CREATE TABLE `flights` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) NOT NULL,
  `flight_name` VARCHAR(255) NOT NULL,
  `flight_code` VARCHAR(50) NOT NULL,
  `itinerary` TEXT NOT NULL,
  `fees` DECIMAL(10,2) NOT NULL,
  `max_passengers` INT(11) NOT NULL,
  `registered_passengers` INT(11) DEFAULT 0,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  `is_completed` TINYINT(1) DEFAULT 0,
  `is_cancelled` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flight_code` (`flight_code`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `flights_fk_company`
    FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- BOOKINGS
-- ==============================

CREATE TABLE `bookings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `flight_id` INT(11) NOT NULL,
  `passenger_id` INT(11) NOT NULL,
  `status` ENUM('pending','registered','completed','cancelled') DEFAULT 'pending',
  `payment_type` ENUM('account','cash') NOT NULL,
  `booking_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_booking` (`flight_id`,`passenger_id`),
  CONSTRAINT `bookings_fk_flight`
    FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `bookings_fk_passenger`
    FOREIGN KEY (`passenger_id`) REFERENCES `passengers` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- MESSAGES
-- ==============================

CREATE TABLE `messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `sender_id` INT(11) NOT NULL,
  `receiver_id` INT(11) NOT NULL,
  `flight_id` INT(11) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `messages_fk_sender`
    FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `messages_fk_receiver`
    FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `messages_fk_flight`
    FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB;

COMMIT;
