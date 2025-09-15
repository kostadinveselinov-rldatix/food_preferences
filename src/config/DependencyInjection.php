<?php

use App\ApiControllers\ApiUserController;
use App\cache\redis\FoodCache\RedisFoodCache;
use App\cache\redis\RedisConfiguration;
use App\cache\redis\UserCache\RedisUsersCache;
use App\config\EntityManagerFactory;
use App\Controllers\UserController;
use App\Repositories\UserRepository;
use App\cache\redis\LimiterCache;
use Predis\Client as PredisClient;
use function DI\create;
use \DI\ContainerBuilder;
use App\Middlewares\RateLimiter;
use App\Repositories\FoodRepository;
use Doctrine\ORM\EntityManager;
use App\Controllers\FoodController;

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

    EntityManager::class => DI\factory([EntityManagerFactory::class, 'getEntityManager']),

    //Cache
    LimiterCache::class => DI\autowire()
        ->constructorParameter('client', DI\get(PredisClient::class))
        ->constructorParameter('expireTime', 30),

    RedisUsersCache::class => DI\autowire()
        ->constructorParameter('redisClient', DI\get(PredisClient::class))
        ->constructorParameter('ttl', 60),
    
    RedisFoodCache::class => DI\autowire()
        ->constructorParameter('redisClient', DI\get(PredisClient::class))
        ->constructorParameter('ttl', 120),
    
    //Middlewares
    RateLimiter::class => function (\Psr\Container\ContainerInterface $c) {
        return new RateLimiter(
            $c->get(\App\cache\redis\LimiterCache::class),
            20,
        );
    },

    //Repositories
    UserRepository::class => DI\autowire()
        ->constructorParameter('redis', DI\get(RedisUsersCache::class))
        ->constructorParameter('em', DI\get(EntityManager::class)),

    FoodRepository::class => function (\Psr\Container\ContainerInterface $c) {
        $em = EntityManagerFactory::getEntityManager();

        $repo = $em->getRepository(\App\Entity\Food::class);

        $repo->setRedis($c->get(RedisFoodCache::class));

        return $repo;
    },

    //Controllers
    UserController::class => DI\autowire()
        ->constructorParameter('userRepository', DI\get(UserRepository::class))
        ->constructorParameter('entityManager', DI\get(EntityManager::class)),
    
    FoodController::class => DI\autowire()
        ->constructorParameter('foodRepository', DI\get(FoodRepository::class)),
    
    ApiUserController::class => DI\autowire(),
];

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions($definitions);
$container = $containerBuilder->build();

return $container;

