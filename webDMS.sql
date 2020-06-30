CREATE TABLE `categories` (
  `ID` int NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categories` (`ID`, `name`) VALUES
(-1, 'Uncategorised');

CREATE TABLE `documents` (
  `ID` int NOT NULL,
  `title` varchar(64) NOT NULL,
  `notes` text NOT NULL,
  `folder` int NOT NULL,
  `upload_date` date NOT NULL,
  `document_date` date NOT NULL,
  `file` varchar(64) NOT NULL,
  `recycle` tinyint(1) NOT NULL DEFAULT '0',
  `recycledate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `folders` (
  `ID` int NOT NULL,
  `title` varchar(64) NOT NULL,
  `category` int NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `users` (
  `ID` int NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `user_salt` varchar(64) NOT NULL,
  `type` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `categories`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `documents`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `folders`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);