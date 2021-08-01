-- Create DB:
CREATE DATABASE video_db;


-- Use DB:
USE video_db;

--Create `videos` table:
CREATE TABLE `videos` (
  `video_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mpd_path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL,
  `video_name` varchar(70) NOT NULL,
  `video_duration` time NOT NULL,
  `processed` tinyint(1) NOT NULL,
  `view_count` int(10) unsigned NOT NULL,
  `video_description` varchar(255) NOT NULL,
  PRIMARY KEY (`video_id`),
  UNIQUE KEY `video_name_2` (`video_name`),
  FULLTEXT KEY `video_name` (`video_name`),
  CONSTRAINT `no_empty_name` CHECK (`video_name` <> '')
);