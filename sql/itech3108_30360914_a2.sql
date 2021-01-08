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
  `name` VARCHAR(255) NOT NULL,
  `text` TEXT NOT NULL,
  `post_date` TIMESTAMP DEFAULT NOW(),
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
INSERT INTO `PowerDrillPost` (
    `name`,
    `text`,
    `post_date`,
    `likes`
  )
VALUES 
  ('John Doe', 'my first cake', '2021-01-05 03:37:17', 2),
  ('Patrick Howard', 'hello world', '2021-01-05 03:38:48', 3),
  ('Gavin Jackson', 'why you should sleep', '2021-01-05 03:39:08', 10),
  ('Ernnest Gray', 'i just bought facebook', '2021-01-05 03:39:34', 5);


