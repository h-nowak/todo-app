<?php
/**
 * User service tests.
 */

namespace App\Tests\Service;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\ORMException;

/**
 * Class UserServiceTest.
 */
class UserServiceTest extends ServiceHelpers
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('1234');
        $user->setEmail('user@user.pl');

        // when
        $this->userService->save($user);

        // then
        $userId = $user->getId();

        /** @var User $resultUser */
        $resultUser = $this->entityManager->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->where('user.id = :id')
            ->setParameter(':id', $userId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($user, $resultUser);
        $this->assertEquals($user->getId(), $resultUser->getId());
        $this->assertEquals($user->getEmail(), $resultUser->getEmail());
        $this->assertEquals($user->getPassword(), $resultUser->getPassword());
        $this->assertEquals($user->getRoles(), $resultUser->getRoles());
        $this->assertEquals($user->getUserIdentifier(), $resultUser->getUserIdentifier());
        $this->assertEquals($user->getSalt(), $resultUser->getSalt());
    }

    /**
     * Test upgrade password.
     */
    public function testUpgradePassword(): void
    {
        // given
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('1234');
        $user->setEmail('user1@user.pl');
        $newPassword = 'new1234';

        // when
        $this->userService->save($user);
        $this->userService->upgradePassword($user, $this->hasher->hashPassword($user, $newPassword));

        // then
        $this->assertTrue($this->hasher->isPasswordValid($user, $newPassword));
    }
}
