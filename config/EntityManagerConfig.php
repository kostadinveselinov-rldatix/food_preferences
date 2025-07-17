<?php
namespace App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;

class EntityManagerFactory
{
    private static ?EntityManager $entityManager = null;

    public static function getEntityManager(): EntityManager
    {
        if (self::$entityManager === null) {
            $paths = [__DIR__ . '/../src/Entity'];
            $isDevMode = true;

            $dbParams = [
                'driver'   => 'pdo_mysql',
                'user'     => 'root',
                'host'     => 'php83-mysql',
                'password' => 'root',
                'dbname'   => 'food_app_db',
            ];

            $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

            $connection = DriverManager::getConnection($dbParams, $config);

            self::$entityManager = new EntityManager($connection, $config);
        }

        return self::$entityManager;
    }
}
