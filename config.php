<?php

function init_db(mysqli $conn) {
    $conn->query(<<<END
    CREATE TABLE IF NOT EXISTS `questions` (
        `question_id` int(11) NOT NULL DEFAULT '0',
        `test_id` int(11) DEFAULT NULL,
        `question` varchar(100) DEFAULT NULL,
        `answer` varchar(100) DEFAULT NULL,
        PRIMARY KEY (`question_id`)
    );
    END);
    $conn->query(<<<END
    INSERT INTO `questions` (`question_id`, `test_id`, `question`, `answer`) VALUES
        (0, 0, 'Is this the last question?', 'No'),
        (1, 0, 'Is this the last question?', 'Yes');
    END);

    $conn->query(<<<END
    CREATE TABLE IF NOT EXISTS `tests` (
        `test_id` int(11) NOT NULL DEFAULT '0',
        `test_name` varchar(100) DEFAULT NULL,
        PRIMARY KEY (`test_id`)
    );
    END);
    $conn->query(<<<END
    INSERT INTO `tests` (`test_id`, `test_name`) VALUES
        (0, 'Beginner'),
        (1, 'Advanced');
    END);

    $conn->query(<<<END
    CREATE TABLE IF NOT EXISTS `testtaken` (
        `testtaken_id` int(11) NOT NULL DEFAULT '0',
        `user_id` int(11) DEFAULT NULL,
        `test_id` int(11) DEFAULT NULL,
        `date_taken` date DEFAULT NULL,
        `score` int(11) DEFAULT NULL,
        `testbreakdown` varchar(100) NOT NULL,
        `wrong_answers` varchar(100) DEFAULT NULL,
        PRIMARY KEY (`testtaken_id`),
        KEY `test_id` (`test_id`),
        KEY `testtaken_ibfk_1` (`user_id`)
    );
    END);

    $conn->query(<<<END
    CREATE TABLE IF NOT EXISTS `users` (
        `userid` int(11) NOT NULL DEFAULT '0',
        `username` varchar(100) DEFAULT NULL,
        `password` varchar(100) DEFAULT NULL,
        `isAdmin` int(1) NOT NULL,
        PRIMARY KEY (`userid`)
    );
    END);
    $conn->query("INSERT INTO `users` (`userid`, `username`, `password`, `isAdmin`) VALUES (0, 'admin', 'pass', 1);");

    $conn->query(<<<END
    ALTER TABLE `testtaken`
        ADD CONSTRAINT `testtaken_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`),
        ADD CONSTRAINT `testtaken_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`);
    END);
}