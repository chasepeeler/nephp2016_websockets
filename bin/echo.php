<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$context = new ZMQContext();
$socket  = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
$socket->connect("tcp://localhost:5555");

while(true){
	$input = stream_get_line(STDIN, 1024, PHP_EOL);
	if($input == "quit") {
		break;
	}

	$data = new stdClass();
	$data->topic = "echos";
	$data->message = $input;

	$message = base64_encode(json_encode($data));
	$socket->send($message);
}

