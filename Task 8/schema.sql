-- create schema shift_planning
CREATE SCHEMA IF NOT EXISTS `shift_planning_test` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;

USE `shift_planning_test`;

-- create table user
CREATE TABLE IF NOT EXISTS `user` (
    `user_id` INT NOT NULL AUTO_INCREMENT,
    `fullname` VARCHAR(128) NOT NULL,
    `email` VARCHAR(512) NOT NULL,
    `country` VARCHAR(128) NOT NULL,
    PRIMARY KEY (`user_id`)
) ENGINE = InnoDB;



-- insert some data to the user table
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Kim Hoover', 'diam.Proin@estmauris.ca', 'Liechtenstein');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Matthew Howe', 'leo@urna.org', 'United Kingdom');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Acton Sheppard', 'ipsum@fringilla.edu', 'Germany');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Madaline Wyatt', 'Duis.a@vitaediam.ca', 'China');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Ciara Johnston', 'elit.Etiam.laoreet@eutempor.com', 'Estonia');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Ella Garza', 'eu.odio.tristique@egetvolutpat.com', 'Mauritania');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Camille Huber', 'diam.vel.arcu@lectus.ca', 'Rwanda');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Ulric Collier', 'mauris.blandit@telluseuaugue.org', 'Burundi');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Venus Odom', 'turpis.nec.mauris@arcuVestibulum.ca', 'Somalia');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Kitra Kramer', 'non@bibendumsedest.org', 'Western Sahara');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Suki Lopez', 'enim.nisl@dictumPhasellus.org', 'Holy See (Vatican City State)');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Hamish Estes', 'pede.Cum.sociis@Cumsociisnatoque.com', 'Northern Mariana Islands');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Glenna Hendricks', 'risus@egetmetus.org', 'Christmas Island');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Kibo Short', 'molestie@ut.com', 'Burundi');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Juliet Rocha', 'Duis@NullafacilisisSuspendisse.com', 'Grenada');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Caleb Floyd', 'metus.Aliquam.erat@cursusluctusipsum.org', 'Ghana');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Kitra Price', 'Curabitur@inconsectetueripsum.com', 'Georgia');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Peter Noel', 'non.sollicitudin.a@orciPhasellus.edu', 'Bulgaria');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Serina Fox', 'at@nisi.edu', 'Germany');
INSERT INTO `user` (`fullname`, `email`, `country`) VALUES ('Quail Sheppard', 'vel@luctusetultrices.edu', 'Malta');