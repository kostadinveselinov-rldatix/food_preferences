<?php
require_once __DIR__ . "/../../bootstrap.php";
require_once \BASE_PATH . "/src/Entity/Food.php";
require_once \BASE_PATH . "/config/EntityManagerConfig.php";

use App\Entity\Food;
use App\EntityManagerFactory;

class  ReportGenerator {
    private $entityManager;

    public function __construct(){
        $this->entityManager = EntityManagerFactory::getEntityManager();
    }

    public function generate(string $time): void {
        $foodRepo = $this->entityManager->getRepository(Food::class);
        $foods = $foodRepo->findAll();

        $csvPath = \BASE_PATH . "/src/reports/report_{$time}.csv";
        $fp = fopen($csvPath, 'w');

        fputcsv($fp, ['Food', 'People']);

        foreach ($foods as $food) {
           
            $foodName = $food->getName();
            $userNames = [];

            foreach ($food->getUsers() as $user) {
                $userNames[] = $user->getName();
            }

            fputcsv($fp, [$foodName, implode(', ', $userNames)]);
        }

        fclose($fp);
    }
}