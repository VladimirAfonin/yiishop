CREATE TABLE `user` (
  `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL
);

INSERT INTO `user` (`id`, `username`, `email`) VALUES (1, 'user', 'user@email.com');