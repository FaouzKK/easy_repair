-- Active: 1728917800550@@127.0.0.1@3306@request_manager
-- Active: 1728917800550@@127.0.0.1@3306
-- Désactivation des vérifications d'intégrité pendant la création
SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;

SET
    @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS,
    FOREIGN_KEY_CHECKS = 0;

SET
    @OLD_SQL_MODE = @@SQL_MODE,
    SQL_MODE = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schéma request_manager
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS request_manager;

CREATE SCHEMA IF NOT EXISTS `request_manager` DEFAULT CHARACTER SET utf8;

USE `request_manager`;

-- -----------------------------------------------------
-- Table `request_manager`.`clients`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `request_manager`.`clients`;

CREATE TABLE IF NOT EXISTS `request_manager`.`clients` (
    `client_id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`client_id`),
    UNIQUE INDEX `email_UNIQUE` (`email`)
) ENGINE = InnoDB;

INSERT INTO
    `request_manager`.`clients` (
        `username`,
        `email`,
        `password`
    )
VALUES (
        'John Doe',
        'john.doe@example.com',
        'password123'
    );

-- -----------------------------------------------------
-- Table `request_manager`.`domains`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `request_manager`.`domains`;

CREATE TABLE IF NOT EXISTS `request_manager`.`domains` (
    `domain_id` INT NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`domain_id`)
) ENGINE = InnoDB;

INSERT INTO
    `request_manager`.`domains` (label)
VALUES ('mecanique'),
    ('electronique'),
    ('electricite'),
    ('plomberie'),
    ('informatique');
-- -----------------------------------------------------
-- Table `request_manager`.`repairmen`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `request_manager`.`repairmen`;

CREATE TABLE IF NOT EXISTS `request_manager`.`repairmen` (
    `repairman_id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`repairman_id`),
    UNIQUE INDEX `email_UNIQUE` (`email`)
) ENGINE = InnoDB;

INSERT INTO
    `request_manager`.`repairmen` (username, email, password)
VALUES (
        'John Doe',
        'john.doe@example.com',
        'password123'
    );

-- -----------------------------------------------------
-- Table `request_manager`.`requests`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `request_manager`.`requests`;

CREATE TABLE IF NOT EXISTS `request_manager`.`requests` (
    `request_id` INT NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `address` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `clients_client_id` INT NOT NULL,
    `domains_domain_id` INT NOT NULL,
    `repairmen_repairman_id` INT NULL,
    PRIMARY KEY (`request_id`),
    CONSTRAINT `fk_requests_clients` FOREIGN KEY (`clients_client_id`) REFERENCES `request_manager`.`clients` (`client_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_requests_domain` FOREIGN KEY (`domains_domain_id`) REFERENCES `request_manager`.`domains` (`domain_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_requests_repairmen` FOREIGN KEY (`repairmen_repairman_id`) REFERENCES `request_manager`.`repairmen` (`repairman_id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `request_manager`.`requests_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `request_manager`.`requests_status`;

CREATE TABLE IF NOT EXISTS `request_manager`.`requests_status` (
    `id_status` INT NOT NULL AUTO_INCREMENT,
    `status_type` ENUM('pending', 'accepted') DEFAULT 'pending',
    `requests_request_id` INT NOT NULL,
    PRIMARY KEY (`id_status`),
    CONSTRAINT `fk_requests_status_requests` FOREIGN KEY (`requests_request_id`) REFERENCES `request_manager`.`requests` (`request_id`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `request_manager`.`repairmen_has_domains`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `request_manager`.`repairmen_has_domains`;

CREATE TABLE IF NOT EXISTS `request_manager`.`repairmen_has_domains` (
    `repairmen_repairman_id` INT NOT NULL,
    `domains_domain_id` INT NOT NULL,
    PRIMARY KEY (
        `repairmen_repairman_id`,
        `domains_domain_id`
    ),
    CONSTRAINT `fk_repairmen_has_domains_repairmen` FOREIGN KEY (`repairmen_repairman_id`) REFERENCES `request_manager`.`repairmen` (`repairman_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_repairmen_has_domains_domain` FOREIGN KEY (`domains_domain_id`) REFERENCES `request_manager`.`domains` (`domain_id`) ON DELETE CASCADE
) ENGINE = InnoDB;


CREATE TRIGGER `auto_insert_request_status`
AFTER
INSERT ON `request_manager`.`requests` FOR EACH ROW
INSERT INTO `request_manager`.`requests_status` (`requests_request_id`) VALUES (NEW.request_id);

-- Rétablissement des vérifications d'intégrité
SET SQL_MODE = @OLD_SQL_MODE;

SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;

SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;