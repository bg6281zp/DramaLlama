USE `silex_blog_a9`;

LOCK TABLES `roles` WRITE;
INSERT INTO `roles` VALUES (3,'ROLE_COMMENTER');
UNLOCK TABLES;

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `comments` text NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION
  );
  
INSERT INTO `user_roles` VALUES (1,3);