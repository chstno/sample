<?php


namespace Tests;


use Core\Support\AllowsTransactionsInterface;
use Models\User;
use PHPUnit\Framework\TestCase;
use Repositories\UserRepository;

class UserRepositoryTest extends TestCase
{

    protected UserRepository $userRepository;
    protected static ?User $user = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = \instance(UserRepository::class);
    }

    /**
     * it's more like integration test, cause mocking for repositories seems as not cool practice
     */
    public function test_create_user()
    {

        $user = new User();
        $user->name = uniqid();
        $user->gender = rand(0, 2);
        $user->birthDate = \DateTime::createFromFormat('U', time());

        $repoConnection = $this->userRepository->getConnection();

        if ($repoConnection instanceof AllowsTransactionsInterface) {
            $startTransaction = $repoConnection->beginTransaction();
            $this->assertSame(true, $startTransaction);
        }

        $lastId =  $this->userRepository->save($user);
        $this->assertIsInt($lastId);
        $this->assertGreaterThan(0, $lastId);

        $findUser = $this->userRepository->find($user);

        $this->assertIsArray($findUser);
        $this->assertNotEmpty($findUser);
        $findUser = $findUser[0];
        $user->id = $findUser->id;

        $this->assertEquals($user, $findUser);

        static::$user = $user;
    }

    public function test_update_user()
    {
        $user = static::$user;
        $user->name = uniqid();
        $user->gender = rand(0,2);

        $affected = $this->userRepository->save($user);
        $this->assertSame(1, $affected);

        $findUser = $this->userRepository->find($user);
        $this->assertIsArray($findUser);
        $this->assertNotEmpty($findUser);
        $findUser = $findUser[0];

        $this->assertEquals($findUser, $user);

        static::$user = $user;
    }

    public function test_delete_user()
    {
        $affected = $this->userRepository->delete(static::$user);
        $this->assertIsInt($affected);
        $this->assertSame(1, $affected);

        $findUser = $this->userRepository->find(static::$user);
        $this->assertEmpty($findUser);

        $repoConnection = $this->userRepository->getConnection();

        if ($repoConnection instanceof AllowsTransactionsInterface) {
            $rollback = $repoConnection->rollback();
            $this->assertSame(true, $rollback);
        }

    }
}