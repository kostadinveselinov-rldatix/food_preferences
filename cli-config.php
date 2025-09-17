<?php
require 'vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\DBAL\DriverManager;

$config = new PhpFile('migrations.php');

$entityManager = \App\config\EntityManagerFactory::getEntityManager();

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));