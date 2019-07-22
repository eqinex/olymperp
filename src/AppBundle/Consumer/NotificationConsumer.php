<?php

//src/Acme/DemoBundle/Consumer/UploadPictureConsumer.php

namespace AppBundle\Consumer;

use GuzzleHttp\Client;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class NotificationConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        $params = json_decode($msg->getBody());
        $client = new Client([
            'proxy' => 'tcp://proxy.at.npo:8080'
        ]);
        $client->request(
            'GET',
            'https://api.tlgrm.ru/bot421603639:AAEWZuyn7sL5AuR0vdHyjGMjxCsT7aLYi-w/sendMessage?' . http_build_query($params)
        );
    }
}