<?php

namespace App\Push;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PushRabbit {

    public function MakeRabbitCall($queues, $message)
    {
        $rabbiturl = '127.0.0.1';
        $rabbitport = 5672;
        $rabbitlogin = 'alteripso';
        $rabbitpass = 'Harestech';

        $connection = new AMQPStreamConnection($rabbiturl, $rabbitport, $rabbitlogin, $rabbitpass);
        $channel = $connection->channel();
        foreach ($queues as $queue) {
            $msg = new AMQPMessage(
                $message,
                array('delivery_mode' => 2)
            );
            $channel->basic_publish($msg, '', $queue);
        }
        $channel->close();
        $connection->close();
    }

    public function rebootBox($id_organisation, $room)
    {
        $queue = $id_organisation . '.' . $room . '.service';
        $message = 'shell%%reboot';     
        $this->MakeRabbitCall([$queue], $message);
    }
    

   

}

?>