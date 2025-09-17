<?php
namespace App\config;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;

require_once __DIR__ . '/../../consts.php';

abstract class EntityManagerFactory
{
    private static ?EntityManager $entityManager = null;

    public static function getEntityManager(): EntityManager
    {
        if (self::$entityManager === null) {
            $paths = [__DIR__ . '/../Entity'];
            $isDevMode = true;

            $dbParams = [
                'driver'   => 'pdo_mysql',
                'user'     => DB_USER ?? 'root',
                'host'     => DB_HOST ?? 'db',
                'password' => DB_PASSWORD ?? 'root',
                'dbname'   => DB_NAME ?? 'food_app_db',
            ];

            $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

            $connection = DriverManager::getConnection($dbParams, $config);

            self::$entityManager = new EntityManager($connection, $config);
        }

        return self::$entityManager;
    }
}
