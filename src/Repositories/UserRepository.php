<?php

namespace App\Repositories;

use App\cache\redis\IUsersCache;
use Doctrine\ORM\EntityRepository;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Redis;

class UserRepository extends EntityRepository
{
    private IUsersCache $redis;

    public function setRedis(IUsersCache $redis)
    {
        if(!isset($this->redis)){
            $this->redis = $redis;
        }
    }

    public function findUserById(int $id, bool $fetchFromCache = true,bool $fetchFood = true):?User
    {
        $user = null;
        if($fetchFromCache)
        {
            $user = $this->redis->getUser((string)$id);
        }

        if(is_null($user))
        {
            $user = $this->find($id);

            if(!is_null($user)){
                if($fetchFood)
                {
                    $user->getFoods()->initialize();
                }
                $this->redis->storeUser($user);
            }
        }

        return $user;
    }

    public function convertUsersObjectsToArray(array $objects):array
    {
        $usersArray = $usersArray = array_map(fn($user) => $user->toArray(), $objects);

        return $usersArray;
    }

    public function findAllUsers(bool $fetchFromCache = true,bool $fetchFood = true,string $key = "all"):array
    {
        $users = null;
        if($fetchFromCache)
        {
            $users = $this->redis->getUsers((string)$key);
        }

        if(is_null($users) )
        {
            if($fetchFood){
                $users = $this->createQueryBuilder('u')
                ->leftJoin('u.foods','f')
                ->addSelect('f')
                ->getQuery()
                ->getResult();
            }else{
                $users = parent::findAll();
            }
             
            if(!empty($users)){       
                $this->redis->storeUsers($key,$users);
            }
        }

        return $users;
    }

    // for API routes because json_encode() cant read private properties from User objects
    public function findAllUsersReturnsArray(bool $fetchFromCache = true,bool $fetchFood = true,string $key = "all"):array
    {
        $users = $this->findAllUsers($fetchFromCache, $fetchFood, $key);
        $users = $this->convertUsersObjectsToArray($users);

        return $users;
    }

    public function storeUser(array $data):User
    {
        if (!isset($data['name'], $data['lastName'], $data['email'])) 
        {
            throw new \InvalidArgumentException("Missing required fields.");
        }
            $name = trim($data['name']);
            $lastName = trim($data['lastName']);
            $email = trim($data['email']);

            $user = new User();
            $user->setName($name);
            $user->setLastname($lastName);
            $user->setEmail($email);
            $user->setCreatedAt(new \DateTime());
           
            $foodIds = $data['foodIds'] ?? [];
            // add preferenced foods to user
            if(is_array($foodIds) && !empty($foodIds)) {
                $foods = $this->getEntityManager()->getRepository(\App\Entity\Food::class)->findBy(['id' => $foodIds]);
                foreach ($foods as $food) {
                    if(!is_null($food)){
                        $user->getFoods()->add($food);
                    }
                }
            }

            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            
            $this->redis->storeUser($user);
            return $user;
    }

    public function updateUser(int $id,array $data):?User
    {
        $user = $this->find($id);
        if ($user === null) {
            return null;
        }

        if (isset($data['name'])) {
            $user->setName(trim($data['name']));
        }
        if (isset($data['lastName'])) {
            $user->setLastname(trim($data['lastName']));
        }
        if (isset($data['email'])) {
            $user->setEmail(trim($data['email']));
        }

        if (isset($data['foodIds']) && is_array($data['foodIds'])) {
            $user->getFoods()->clear();

            $foods = $this->getEntityManager()->getRepository(\App\Entity\Food::class)->findBy(['id' => $data['foodIds']]);
            foreach ($foods as $food) {
                if ($food !== null) {
                    $user->getFoods()->add($food);
                }
            }
        }

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }
    
    public function deleteUser(int $id): bool
    {
        $user = $this->find($id);
        if ($user === null) {
            return false;
        }

        $em = $this->getEntityManager();
        $em->remove($user);
        $em->flush();

        return true;
    }

    public function searchByNameOrLastName(string $searchTerm):array
    {
        $loadedFromCache = $this->redis->getUsers($searchTerm);

        if(is_null($loadedFromCache)){
            $users = $this->getEntityManager()->createQueryBuilder('s')
            ->select('u', 'f')
            ->from(User::class, 'u')
            ->leftJoin("u.foods", 'f')
            ->where('u.name LIKE :searchTerm')
            ->orWhere('u.lastname LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getArrayResult();

            if(!empty($users)){
                $this->redis->storeUsers($searchTerm,$users);
            }
        }else{
            $users = $loadedFromCache;
        }

        return $users;
    }
}