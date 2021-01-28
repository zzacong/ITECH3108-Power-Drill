-- 
-- ITECH3108 Assignment 2
-- ZHI ZAO ONG
-- 30360914
-- 

-- 
-- 
-- Drop Database if exists
DROP DATABASE IF EXISTS `itech3108_30360914_a2`;


-- 
-- 
-- Create Database
CREATE DATABASE `itech3108_30360914_a2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `itech3108_30360914_a2`;


-- 
-- 
-- Create MySQL user for this application
CREATE USER 'powerdrill'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON `itech3108_30360914_a2`.* TO 'powerdrill'@'localhost';


-- 
-- 
-- Drop Tables if exists
DROP TABLE IF EXISTS `PowerDrillPost`;


-- 
-- 
-- Create Tables
CREATE TABLE `PowerDrillPost` (
  `id` SERIAL,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `text` TEXT NOT NULL,
  `post_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `likes` INT DEFAULT 0,
  `reply_to` BIGINT UNSIGNED,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`reply_to`) REFERENCES `PowerDrillPost`(`id`)
);


-- 
-- 
-- INSERT ROWS

-- 
-- PowerDrillPost Table
-- 
-- TOP LEVEL POSTS
-- Name
INSERT INTO `PowerDrillPost` 
  (`name`, `text`, `post_date`, `likes`)
VALUES 
  ('John Doe', 'My old power drill just died yesterday. Looking for a new cordless drill. Anyone has any recommendation?', '2021-01-05 20:00:00', 6),
  ('Patrick Howard', "Been using the Grapevine EWT997 20V XR brushless hammer drill driver. It's the best I've used so far.", '2021-01-06 13:00:00', 11),
  ('user30360914', "What's the difference between an impact drill and hammer drill?", '2021-01-06 15:00:00', 20);

-- Anonymous
INSERT INTO `PowerDrillPost` 
  (`text`, `post_date`)
VALUES 
  ('Is the R8112 Hammer Drill from Grapevine a right choice?', '2021-01-07 18:25:00');


-- 
-- REPLIES
-- Anonymous
INSERT INTO `PowerDrillPost` 
  (`text`, `post_date`, `likes`, `reply_to`)
VALUES
  ("If you're on a budget. The BP100 from Grapevine is an excellent choice. It has 3 hours battery, 20V and four-speed drill.", '2021-01-08 10:20:00', 7, 1),
  ('This is hands down the best cordless drill you can find on the market right now.', '2021-01-08 10:25:00', 4, 2);

-- Name
INSERT INTO `PowerDrillPost`
  (`name`, `text`, `post_date`, `likes`, `reply_to`)
VALUES
  ('Farrington Durham', 'Affordable best 18V cordless drill presents an outstanding value at just $139. Check it out.', '2021-01-08 13:20:00', 6, 1),
  ('user30360914', "I've been using the older version EWT887 and it's still working fine.", '2021-01-08 14:45:00', 18, 2);