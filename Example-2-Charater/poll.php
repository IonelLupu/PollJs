<?php 

$database['host']		= '127.0.0.1';
$database['user']		= 'root';
$database['user_pw']	= '';
$database['dbname']		= '_test_classes';

include "poll.server.php";

Poll::init($database);

Poll::on("move",function($data){
	//Poll::send(Poll::get());
	Poll::emit("move",$data);
});

 ?>
