-- MySQL Script generated by MySQL Workbench
-- 12/15/14 15:39:33
-- Model: New Model    Version: 1.0
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema blog_base
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `blog_base` ;
CREATE SCHEMA IF NOT EXISTS `blog_base` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `blog_base` ;

-- -----------------------------------------------------
-- Table `blog_base`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blog_base`.`user` ;

CREATE TABLE IF NOT EXISTS `blog_base`.`user` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(16) NOT NULL,
  `email` VARCHAR(255) NULL,
  `password` VARCHAR(255) NOT NULL,
  `visible_name` VARCHAR(45) NULL,
  `rank` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`user_id`));


-- -----------------------------------------------------
-- Table `blog_base`.`category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blog_base`.`category` ;

CREATE TABLE IF NOT EXISTS `blog_base`.`category` (
  `category_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `parent_id` INT NULL,
  PRIMARY KEY (`category_id`));


-- -----------------------------------------------------
-- Table `blog_base`.`posts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `blog_base`.`posts` ;

CREATE TABLE IF NOT EXISTS `blog_base`.`posts` (
  `idposts` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `category_parent_id` INT NOT NULL,
  `topic` VARCHAR(255) NOT NULL,
  `text` TEXT NOT NULL,
  `create_date` DATETIME NOT NULL,
  `update_date` DATETIME NULL,
  `image_link` VARCHAR(255) NULL,
  `visible` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`idposts`),
  INDEX `fk_posts_user_idx` (`user_id` ASC),
  INDEX `fk_posts_category1_idx` (`category_id` ASC))
ENGINE = InnoDB;
USE `blog_base` ;
INSERT INTO `category` (`category_id`,`name`,`parent_id`) VALUES (1,'Home',0);
USE `blog_base` ;
INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `visible_name`, `rank`)
VALUES (1, 'admin', '', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', 'user_mainadmin');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;