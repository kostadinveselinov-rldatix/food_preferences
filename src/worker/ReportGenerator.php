<?php
require_once __DIR__ . "/../../bootstrap.php";
require_once \BASE_PATH . "/src/Entity/Food.php";

use App\Entity\Food;

class ReportGenerator {
    private $entityManager;

    public function __construct(){
        $this->entityManager = require_once \BASE_PATH . "/config/EntityManagerConfig.php";
    }

    public function generate(): void {
        $foodRepo = $this->entityManager->getRepository(Food::class);
        $foods = $foodRepo->findAll();

        $csvPath = \BASE_PATH . '/src/reports/report.csv';
        $fp = fopen($csvPath, 'w');

        // CSV Header
        fputcsv($fp, ['Food', 'People']);

        foreach ($foods as $food) {
            /** @var Food $food */
            $foodName = $food->getName();
            $userNames = [];

            foreach ($food->getUsers() as $user) {
                /** @var User $user */
                $userNames[] = $user->getName();
            }

            fputcsv($fp, [$foodName, implode(', ', $userNames)]);
        }

        fclose($fp);
    }
}