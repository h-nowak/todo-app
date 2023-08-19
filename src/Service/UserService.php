<?php
/**
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * User repository.
     */
    private UserRepository $userRepository;

    /**
     * Constructor.
     *
     * @param UserRepository $userRepository User repository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Save user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Upgrade password.
     *
     * @param User   $user     User
     * @param string $password Password
     */
    public function upgradePassword(User $user, string $password): void
    {
        $this->userRepository->upgradePassword($user, $password);
    }
}
