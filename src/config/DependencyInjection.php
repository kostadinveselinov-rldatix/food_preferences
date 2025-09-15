<?php

use App\ApiControllers\ApiUserController;
use App\cache\redis\RedisConfiguration;
use App\cache\redis\RedisUsersCache;
use App\config\EntityManagerFactory;
use App\Controllers\UserController;
use App\Repositories\UserRepository;
use App\cache\redis\LimiterCache;
use Predis\Client as PredisClient;
use function DI\create;
use \DI\ContainerBuilder;
use App\Middlewares\RateLimiter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

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

    LimiterCache::class => DI\autowire()
        ->constructorParameter('client', DI\get(PredisClient::class))
        ->constructorParameter('expireTime', 30),

    RateLimiter::class => function (\Psr\Container\ContainerInterface $c) {
        return new RateLimiter(
            $c->get(\App\cache\redis\LimiterCache::class),
            20,
        );
    },

    EntityManager::class => DI\factory([EntityManagerFactory::class, 'getEntityManager']),

    RedisUsersCache::class => DI\autowire()
        ->constructorParameter('redisClient', DI\get(PredisClient::class))
        ->constructorParameter('ttl', 60),

    UserRepository::class => DI\autowire()
        ->constructorParameter('redis', DI\get(RedisUsersCache::class))
        ->constructorParameter('em', DI\get(EntityManager::class)),


    UserController::class => DI\autowire()
        ->constructorParameter('userRepository', DI\get(UserRepository::class))
        ->constructorParameter('entityManager', DI\get(EntityManager::class)),
];

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions($definitions);
$container = $containerBuilder->build();

return $container;

