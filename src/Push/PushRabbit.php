<?php

namespace App\Push;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PushRabbit {
   public $rabbiturl = '192.168.1.25';
   public $rabbitport = 5672;
   public $rabbitlogin = 'alteripso';
   public $rabbitpass = 'Harestech';

    public function MakeRabbitCall($queues, $message)
    {
       

        $connection = new AMQPStreamConnection($this->rabbiturl, $this->rabbitport, $this->rabbitlogin, $this->rabbitpass);
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

    public function isConnected($id_organisation, $room)
    {
        $connection = new AMQPStreamConnection($this->rabbiturl, $this->rabbitport, $this->rabbitlogin, $this->rabbitpass);
        $channel = $connection->channel();
        $arru = array();
        foreach ($room as $box) {
            $rm = $box->getNom();
            list(,,$consumerCount) =  $channel->queue_declare($id_organisation.".".$rm.".service", false, true, false, false);
            $arru[] = $consumerCount;
        }
        return $arru;
    }

   


    

   

}

?>