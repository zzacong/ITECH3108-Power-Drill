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
-- Drop Tables if exists
DROP TABLE IF EXISTS `PowerDrillPost`;


-- 
-- 
-- Create Tables
CREATE TABLE `PowerDrillPost` (
  `id` SERIAL,
  `name` VARCHAR(255) DEFAULT 'anonymous',
  `text` TEXT NOT NULL,
  `post_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `likes` INT DEFAULT 0,
  `reply_to` BIGINT UNSIGNED,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`reply_to`) REFERENCES `PowerDrillPost`(`id`)
);


-- 
-- 
-- INSERT ROW

-- 
-- PowerDrillPost Table
-- 
-- TOP LEVEL POSTS
-- Name
INSERT INTO `PowerDrillPost` 
  (`name`, `text`, `post_date`, `likes`)
VALUES 
  ('John Doe', 'my first cake', '2021-01-05 20:00:00', 2),
  ('Patrick Howard', 'hello world', '2021-01-06 13:00:00', 3),
  ('Gavin Jackson', 'why you should sleep', '2021-01-06 15:00:00', 10),
  ('Ernnest Gray', 'i just bought facebook', '2021-01-07 10:20:00', 5);

-- Anonymous
INSERT INTO `PowerDrillPost` 
  (`text`, `post_date`)
VALUES 
  ('Some birds fly over the sky', '2021-01-07 18:25:00'),
  ('Homebrew help you become greater devloper', '2021-01-08 09:45:00');


-- 
-- REPLIES
-- Anonymous
INSERT INTO `PowerDrillPost` 
  (`text`, `post_date`, `reply_to`)
VALUES
  ('donut car bombibi', '2021-01-08 10:20:00', 1),
  ('the superman return bombibi', '2021-01-08 10:25:00', 2);

-- Name
INSERT INTO `PowerDrillPost`
  (`name`, `text`, `post_date`, `reply_to`)
VALUES
  ('Farrington Durham', 'pizza hawaiian bombibi', '2021-01-08 13:20:00', 1),
  ('Steve Iee', 'game of throne blah bombibi', '2021-01-08 14:45:00', 2);