<?php
namespace App\worker;
require_once __DIR__ . "/../../bootstrap.php";

use App\Entity\Food;
use App\config\EntityManagerFactory;

class ReportGenerator {
    private $entityManager;
    private $filePath =  \BASE_PATH . "/src/reports/";

    public function __construct(){
        $this->entityManager = EntityManagerFactory::getEntityManager();
    }

    public function generate(string $time): void {
        $this->entityManager->clear();
        $foodRepo = $this->entityManager->getRepository(Food::class);
        $foods = $foodRepo->findAll();

        $csvPath = $this->filePath . "report_{$time}.csv";
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

    public function deleteReport(string $fileName): bool {
        $csvPath = $this->filePath . "report_{$fileName}.csv";

        if(file_exists($csvPath)){
            return unlink($csvPath);
        } else {
            return false;
        }
    }
}