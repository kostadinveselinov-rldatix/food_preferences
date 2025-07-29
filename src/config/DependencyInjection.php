<?php

use App\ApiControllers\ApiUserController;
use App\cache\redis\RedisConfiguration;
use App\cache\redis\RedisUsersCache;
use App\config\EntityManagerFactory;
use App\Repositories\UserRepository;
use Predis\Client as PredisClient;
use function DI\create;
use \DI\ContainerBuilder;


$definitions = [

    // Manually define Redis configuration
    RedisConfiguration::class => create()
        ->constructor("tcp",'redis', 6379,"",0),

    PredisClient::class => function (\Psr\Container\ContainerInterface $c) {
        $config = $c->get(RedisConfiguration::class);
        return new PredisClient([
            'scheme' => $config->scheme,
            'host'   => $config->databaseHost,
            'port'   => $config->databasePort,
            'password' => $config->password,
            "database" => $config->databaseConnection,
        ]);
    },

    RedisUsersCache::class => DI\autowire(),

    UserRepository::class => function (\Psr\Container\ContainerInterface $c) {
        $em = EntityManagerFactory::getEntityManager();

        $repo = $em->getRepository(\App\Entity\User::class);

        $repo->setRedis($c->get(RedisUsersCache::class));

        return $repo;
    },

    ApiUserController::class => DI\autowire()
        ->constructorParameter('userRepository', DI\get(UserRepository::class)),
];

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions($definitions);
$container = $containerBuilder->build();

return $container;

