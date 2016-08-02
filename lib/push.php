<?php

class Push
{

	protected static $socketCache = [];

	/**
	 * @param string        $topic
	 * @param mixed         $message
	 *
	 * @param bool|callable $serialize
	 *
	 * @return $this
	 */
	public function sendMessage($topic, $message, $serialize = true)
	{
		if (class_exists("\\ZMQContext") && class_exists("\\ZMQSocket")) {
			if (defined("PUSH_SERVERS")) {
				$servers = explode(",", PUSH_SERVERS);
			} else {
				$servers = ["tcp://localhost:5555"];
			}
			$context = new \ZMQContext();

			$data        = new \stdClass;
			$data->topic = $topic;
			if (true === $serialize) {
				$message = json_encode($message);
			} elseif (is_callable($serialize)) {
				$message = call_user_func($serialize, $message);
			}
			$data->message = $message;
			$data = base64_encode(json_encode($data));

			foreach ($servers as $server) {
				$socket = $this->getSocket($context, $server);
				$socket->send($data);
			}

		}
		return $this;
	}

	/**
	 * @param \ZMQContext $context
	 * @param string      $server
	 *
	 * @return \ZMQSocket
	 */
	protected function getSocket($context, $server)
	{
		if (!array_key_exists($server, self::$socketCache)) {
			$socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
			$socket->connect($server);
			self::$socketCache[$server] = $socket;
		}
		return self::$socketCache[$server];
	}

}