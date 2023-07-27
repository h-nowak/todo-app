<?php
/**
 * Category service interface.
 */

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class CategoryServiceInterface.
 */
interface CategoryServiceInterface
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
