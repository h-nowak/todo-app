<?php
/**
 * Security controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SecurityControllerTest.
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test login.
     */
    public function testLogin(): void
    {
        // given
        $client = $this->httpClient;
        $code = 200;

        // when
        $client->request('GET', '/login');
        $resultCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals($code, $resultCode);
    }

    /**
     * Test logout.
     */
    public function testLogout(): void
    {
        // given
        $client = $this->httpClient;
        $code = 302;

        // when
        $client->request('GET', '/logout');
        $resultCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals($code, $resultCode);
    }

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     */
    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail('user2@example.com');
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }
}
