<?php
/**
 * TodoList service.
 */

namespace App\Service;

use App\Repository\TodoListRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TodoListService.
 */
class TodoListService implements TodoListServiceInterface
{
    /**
     * TodoList repository.
     */
    private TodoListRepository $todoListRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param TodoListRepository $todoListRepository TodoList repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(TodoListRepository $todoListRepository, PaginatorInterface $paginator)
    {
        $this->todoListRepository = $todoListRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->todoListRepository->queryAll(),
            $page,
            TodoListRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }
}
