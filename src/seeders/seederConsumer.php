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
$emConnection = $em->getConnection();
$container = require_once __DIR__ . "/../config/DependencyInjection.php";
$foodRepository = $container->get(\App\Repositories\FoodRepository::class);

$queues = ["create_user", "create_food"];

foreach($queues as $queue) {
    $ch->queue_declare($queue, false, true, false, false);

    $ch->basic_consume($queue, '', false, true, false, false, function($msg) use ($queue, $em,$emConnection,$foodRepository,$container) {
        $data = json_decode($msg->getBody(), true)['data'] ?? null;

        if ($data) {
            echo "Processing message from {$queue}: \n";
            
            if ($queue === "create_user") {
                $emConnection->beginTransaction();

                try{
                    foreach($data as $userData)
                    {
                        createUser($userData, $em, false);
                    }
                    $em->flush(); // flush once after all
                    $emConnection->commit();

                    $container->get(\App\cache\redis\UserCache\RedisUsersCache::class)->invalidateAllUsersCache();
                } catch (\Exception $e) {
                    $emConnection->rollBack();
                    echo "Failed to process message from {$queue}: " . $e->getMessage() . "\n";
                }

            } elseif ($queue === "create_food") {
                $emConnection->beginTransaction();

                try{
                    foreach($data as $foodData)
                    {
                        createFood($foodData, $em, false);
                    }
                    $em->flush();
                    // foodBulkAdd($data,$em);
                    $emConnection->commit();

                    $container->get(\App\cache\redis\FoodCache\RedisFoodCache::class)->invalidateAllFoodsCache();
                } catch (\Exception $e) {
                    $emConnection->rollBack();
                    echo "Failed to process message from {$queue}: " . $e->getMessage() . "\n";
                }
            }
            
        } else {
            echo "Received empty data on {$queue}\n";
        }
    });
}

while ($ch->is_consuming()) {
    $ch->wait();
}

function createUser(array $data, $em, $flush = true) {
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
    if ($flush) {
        $em->flush();
    }
}

function createFood(array $data, $em, $flush = true) {
    $food = new \App\Entity\Food();
    $food->setName($data['name']);

    $em->persist($food);
    if ($flush) {
        $em->flush();
    }

}

function foodBulkAdd(array $data, $em){
    $conn = $em->getConnection();

    if (empty($data)) {
        return;
    }

    $values = [];
    $params = [];
    foreach ($data as $i => $entry) {
        $values[] = "(:name{$i})";
        $params["name{$i}"] = $entry['name'];
    }

    $query = "INSERT INTO foods (name) VALUES " . implode(", ", $values);

    $stmt = $conn->prepare($query);
    $stmt->executeStatement($params);
}