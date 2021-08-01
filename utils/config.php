<?php
//DB conection
$database="video_db";
$database_user=""; //USER for logging into the DB
$database_pwd=""; //PASSWORD for logging into the DB
$host="localhost";

//Queries
$upload_q='INSERT INTO videos(mpd_path, thumbnail_path, upload_date, video_name, video_duration, processed, view_count, video_description) VALUES (?,?,?,?,?,?,?,?)';
$process_q='UPDATE videos SET processed=? WHERE video_id=?';
$random_q='SELECT * FROM videos WHERE processed != 0 ORDER BY rand() LIMIT 12';
$random_lim_q='SELECT * FROM videos WHERE video_id != ? AND processed !=0 ORDER BY rand() LIMIT 12';
$id_q='SELECT * FROM videos WHERE video_id=?';
$search_q="SELECT * from videos WHERE MATCH(video_name) AGAINST( ? IN NATURAL LANGUAGE MODE)";
$update_views="UPDATE videos SET view_count = view_count + 1 WHERE video_id=?";

//MYSQL Error
$empty_name=4025;
$name_exists=1062;

//RABITMQ conection
$rabbitmq_user=""; //USER for logging into RabbitMQ session
$rabbitmq_pwd=""; //PASSWORD for logging into RabbitMQ session
$rabbitmq_port=0000; //PORT in which the RabbitMQ session is listening
$rabbitmq_queue_name=""; //Name of the queue of the RabbitMQ session

//Utils
$dir_p = "uploads/";
$dir_a = "../../api/";
?>
