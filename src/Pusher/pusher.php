<?php
namespace Pusher;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {

  /**
   * @var Topic[]
   */
  protected $subscribedTopics = [];

  /**
   * A request to subscribe to a topic has been made
   * @param \Ratchet\ConnectionInterface $conn
   * @param Topic                 $topic The topic to subscribe to
   */
  public function onSubscribe(ConnectionInterface $conn, $topic)
  {
    $this->subscribedTopics[$topic->getId()] = $topic;
  }

  /**
   * A message is received
   * @param string $message the message
   */
  public function onMessage($message){
    $data = json_decode(base64_decode($message));

    if(!array_key_exists($data->topic,$this->subscribedTopics)){
      return;
    }

    $topic = $this->subscribedTopics[$data->topic];
    $topic->broadcast($data->message);
  }


  
  /**
   * When a new connection is opened it will be passed to this method
   * @param  ConnectionInterface $conn The socket/connection that just connected to your application
   * @throws \Exception
   */
  function onOpen(ConnectionInterface $conn)
  {
    // TODO: Implement onOpen() method.
  }

  /**
   * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
   *
   * @param  ConnectionInterface $conn The socket/connection that is closing/closed
   *
   * @throws \Exception
   */
  function onClose(ConnectionInterface $conn)
  {
    // TODO: Implement onClose() method.
  }

  /**
   * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
   * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
   *
   * @param  ConnectionInterface $conn
   * @param  \Exception          $e
   *
   * @throws \Exception
   */
  function onError(ConnectionInterface $conn, \Exception $e)
  {
    // TODO: Implement onError() method.
  }

  /**
   * An RPC call has been received
   *
   * @param \Ratchet\ConnectionInterface $conn
   * @param string                       $id     The unique ID of the RPC, required to respond to
   * @param string|Topic                 $topic  The topic to execute the call against
   * @param array                        $params Call parameters received from the client
   */
  function onCall(ConnectionInterface $conn, $id, $topic, array $params)
  {
    // In this application if clients send data it's because the user hacked around in console
    $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
  }


	/**
	 * A client is attempting to publish content to a subscribed connections on a URI
	 *
	 * @param \Ratchet\ConnectionInterface $conn
	 * @param string|Topic                 $topic    The topic the user has attempted to publish to
	 * @param string                       $event    Payload of the publish
	 * @param array                        $exclude  A list of session IDs the message should be excluded from (blacklist)
	 * @param array                        $eligible A list of session Ids the message should be send to (whitelist)
	 */
	function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
	{
		// In this application if clients send data it's because the user hacked around in console
		$conn->close();
	}


	/**
   * A request to unsubscribe from a topic has been made
   *
   * @param \Ratchet\ConnectionInterface $conn
   * @param string|Topic                 $topic The topic to unsubscribe from
   */
  function onUnSubscribe(ConnectionInterface $conn, $topic)
  {
    // TODO: Implement onUnSubscribe() method.
  }


}