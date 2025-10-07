<?php

namespace App\Repositories;

use App\cache\redis\UserCache\IUsersCache;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repositories\Interfaces\UserRepositoryInterface;


class UserRepository implements UserRepositoryInterface
{
    private IUsersCache $redis;
    private $em;

    public function __construct(IUsersCache $redis,EntityManagerInterface $em)
    {
        $this->redis = $redis;
        $this->em = $em;
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
            $user = $this->em->getRepository(User::class)->find($id);

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
                $users = $this->em->createQueryBuilder()
                ->select('u', 'f')
                ->from(User::class, 'u')
                ->leftJoin('u.foods', 'f')
                ->orderBy('u.createdAt','DESC')
                ->getQuery()
                ->getResult();
            }else{
                $users = $this->em->getRepository(User::class)->findAll();
            }
             
            if(!empty($users)){       
                $this->redis->storeUsers($users,$key);
            }
        }

        return $users;
    }

    // for API routes because json_encode() cant read private properties from User objects
    public function findAllUsersReturnsArray(bool $fetchFromCache = true,bool $fetchFood = true,string $key = "all"):array
    {
        return $this->em->createQueryBuilder()
                ->select('u', 'f')
                ->from(User::class, 'u')
                ->leftJoin('u.foods', 'f')
                ->orderBy('u.createdAt','DESC')
                ->getQuery()
                ->getArrayResult();
    }

    public function storeUser(string $name,string $lastName,string $email,array $foodIds = []):User
    {
            $user = new User();
            $user->setName($name);
            $user->setLastname($lastName);
            $user->setEmail($email);
            $user->setCreatedAt(new \DateTime());
 
            // add preferenced foods to user
            if(is_array($foodIds) && !empty($foodIds)) {
                $foods = $this->em->getRepository(\App\Entity\Food::class)->findBy(['id' => $foodIds]);
                foreach ($foods as $food) {
                    if(!is_null($food)){
                        $user->getFoods()->add($food);
                    }
                }
            }

            $this->em->persist($user);
            $this->em->flush();
            
            $this->redis->storeUser($user);
            return $user;
    }

    public function updateUser(int $id,array $data):?User
    {
        $user = $this->em->find(User::class, $id);
        if ($user === null) {
            return null;
        }

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        if (isset($data['lastName'])) {
            $user->setLastname($data['lastName']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['foodIds']) && is_array($data['foodIds'])) {
            $user->getFoods()->clear();

            $foods = $this->em->getRepository(\App\Entity\Food::class)->findBy(['id' => $data['foodIds']]);
            foreach ($foods as $food) {
                if ($food !== null) {
                    $user->getFoods()->add($food);
                }
            }
        }

        $this->em->persist($user);
        $this->em->flush();

        $this->redis->storeUser($user);
        return $user;
    }
    
    public function deleteUser(int $id): bool
    {
        $user = $this->em->find(User::class,$id);
        if ($user === null) {
            return false;
        }

        $this->em->remove($user);
        $this->em->flush();

        $this->redis->deleteUser((string)$id);

        return true;
    }

    public function searchByNameOrLastName(string $searchTerm):array
    {
        $loadedFromCache = $this->redis->getUsers($searchTerm);

        if(is_null($loadedFromCache)){
            $users = $this->em->createQueryBuilder()
            ->select('u', 'f')
            ->from(User::class, 'u')
            ->leftJoin("u.foods", 'f')
            ->where('u.name LIKE :searchTerm')
            ->orWhere('u.lastname LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getArrayResult();

            if(!empty($users)){
                $this->redis->storeUsers($users,$searchTerm);
            }
        }else{
            $users = $loadedFromCache;
        }

        return $users;
    }

    public function fetchUsersInBatches(int $page = 0, int $batchSize = 10, $hyrdated = false):array
    {
        $query = $this->em->createQueryBuilder()
            ->select('u', 'f')
            ->from(User::class, 'u')
            ->leftJoin('u.foods', 'f')
            ->setFirstResult($page * $batchSize)
            ->setMaxResults($batchSize)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery();
        
            $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
            
            if($hyrdated){
                return $query->getResult();
            }

            return $query->getArrayResult();
    }

    public function countAllUsers(): int
    {
        return $this->em->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->getQuery()
            ->getSingleScalarResult();
    }

}