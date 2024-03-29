<?php
/**
 * TodoList service interface.
 */

namespace App\Service;

use App\Entity\TodoList;
use App\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class TodoListServiceInterface.
 */
interface TodoListServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface;

    /**
     * Save entity.
     *
     * @param TodoList $todoList TodoList entity
     */
    public function save(TodoList $todoList): void;

    /**
     * Delete entity.
     *
     * @param TodoList $todoList TodoList entity
     */
    public function delete(TodoList $todoList): void;

    /**
     * Can TodoList be deleted?
     *
     * @param TodoList $todoList TodoList entity
     *
     * @return bool Result
     */
    public function canBeDeleted(TodoList $todoList): bool;

    /**
     * Find by id.
     *
     * @param int $id Category id
     *
     * @return TodoList|null Category entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?TodoList;
}
