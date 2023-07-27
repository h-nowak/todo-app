<?php
/**
 * TodoList service interface.
 */

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class TodoListServiceInterface.
 */
interface TodoListServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;
}
