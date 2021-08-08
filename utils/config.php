<?php
//DB conection
$database="video_db";
$database_user=""; //USER for logging into the DB
$database_pwd=""; //PASSWORD for logging into the DB
$host="localhost";

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
