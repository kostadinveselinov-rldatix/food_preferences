<?php

declare(ticks=1);

use \App\config\EntityManagerFactory;

// require_once __DIR__ . "/../../bootstrap.php";
require_once __DIR__ . "/../../vendor/autoload.php";


use PhpAmqpLib\Connection\AMQPStreamConnection;

for ($i = 0; $i < 5; $i++) {
    try {
        $conn = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        break;
    } catch (\Exception $e) {
        echo "Waiting for RabbitMQ2...\n";
        sleep(3);
    }
}
echo "connected";

$ch = $conn->channel();

$em = EntityManagerFactory::getEntityManager();

$queues = ["create_user", "create_food"];

foreach($queues as $queue) {
    $ch->queue_declare($queue, false, true, false, false);

    $ch->basic_consume($queue, '', false, true, false, false, function($msg) use ($queue, $em) {
        $data = json_decode($msg->getBody(), true)['data'] ?? null;
        echo "here";
        if ($data) {
            echo "Processing message from {$queue}: \n";
            
            if ($queue === "create_user") {
                foreach($data as $userData)
                {
                    createUser($userData, $em);
                }
            } elseif ($queue === "create_food") {
                foreach($data as $foodData)
                {
                    sleep(1);
                    createFood($foodData, $em);
                }
            }
            
        } else {
            echo "Received empty data on {$queue}\n";
        }
    });
}

function createUser(array $data, $em) {
    $user = new \App\Entity\User();
    $user->setName($data['name']);
    $user->setLastName($data['lastname']);
    $user->setEmail($data['email']);
    $user->setCreatedAt(new \DateTime());

    if (!empty($data['foodIds'])) {
        $foods = $em->getRepository(\App\Entity\Food::class)->findBy(['id' => $data['foodIds']]);
        foreach ($foods as $food) {
            if ($food !== null) {
                $user->addFood($food);
            }
        }
    }

    $em->persist($user);
    $em->flush();

    echo "User created with ID: " . $user->getId() . "\n";
}

function createFood(array $data, $em) {
    $food = $em->getRepository(\App\Entity\Food::class)->storeFood($data['name']);

    echo "Food created with ID: " . $food->getId() . "\n";
}

while ($ch->is_consuming()) {
    $ch->wait();
}
