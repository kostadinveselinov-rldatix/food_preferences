<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;
use App\cache\redis\IUsersCache;
use App\Entity\Food;

class FoodRepository extends EntityRepository
{

}