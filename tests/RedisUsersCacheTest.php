<?php
declare(strict_types= 1);

use PHPUnit\Framework\TestCase;
use \App\cache\redis\RedisUsersCache;
use \App\Entity\User;
use \Predis\Client as PredisClient;

final class RedisUsersCacheTest extends TestCase
{
   private $predisMock;
   private RedisUsersCache $cache;

   protected function setUp():void
   {
    $this->predisMock = $this->createMock(PredisClient::class);

    $this->cache = new RedisUsersCache($this->predisMock);
   }

   public function testStoreUserCallsPredisSet():void
   {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(123);

        $serializedUser = serialize($user);
        $expectedKey = 'user_key_123';
        $expectedTTL = 60;

        $this->predisMock->expects($this->once())
            ->method("__call")
            ->with(
     $this->equalTo('set'),
                $this->callback(function ($args) use ($expectedKey, $expectedTTL) {
                return is_array($args)
                    && $args[0] === $expectedKey
                    && is_string($args[1]) // serialized user object
                    && $args[2] === 'EX'
                    && $args[3] === $expectedTTL;
            })
            );
        
        $this->cache->storeUser($user);
   }

   public function testGetUserReturnsUnserializedUser()
    {
        $userId = '123';
        $key = 'user_key_123';

        $user = $this->createMock(User::class);
        $serializedUser = serialize($user);

        $this->predisMock->expects($this->once())
            ->method('__call')
            ->with(
     $this->equalTo("get"),
                $this->equalTo([$key])
            )
            ->willReturn($serializedUser);

        $result = $this->cache->getUser($userId);

        // Assert the returned object is unserialized correctly
        $this->assertEquals($user, $result);
    }

    public function testGetUserReturnsNullIfNotFound()
    {
        $userId = '999';
        $key = 'user_key_999';

        $this->predisMock->expects($this->once())
            ->method('__call')
            ->with(
     $this->equalTo('get'),
                $this->equalTo([$key])
            )
            ->willReturn(null);

        $result = $this->cache->getUser($userId);

        $this->assertNull($result);
    }
}