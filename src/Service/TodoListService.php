<?php
/**
 * TodoList service.
 */

namespace App\Service;

use App\Entity\TodoList;
use App\Entity\User;
use App\Repository\NoteRepository;
use App\Repository\TodoListRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
     * Note repository.
     */
    private NoteRepository $noteRepository;

    /**
     * Constructor.
     *
     * @param TodoListRepository $todoListRepository TodoListRepository
     * @param PaginatorInterface $paginator          PaginatorInterface
     * @param NoteRepository     $noteRepository     NoteRepository
     */
    public function __construct(TodoListRepository $todoListRepository, PaginatorInterface $paginator, NoteRepository $noteRepository)
    {
        $this->todoListRepository = $todoListRepository;
        $this->paginator = $paginator;
        $this->noteRepository = $noteRepository;
    }

    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->todoListRepository->queryByAuthor($author),
            $page,
            TodoListRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param TodoList $todoList TodoList entity
     */
    public function save(TodoList $todoList): void
    {
        $this->todoListRepository->save($todoList);
    }

    /**
     * Delete entity.
     *
     * @param TodoList $todoList TodoList entity
     */
    public function delete(TodoList $todoList): void
    {
        $this->todoListRepository->delete($todoList);
    }

    /**
     * Can TodoList be deleted?
     *
     * @param TodoList $todoList TodoList entity
     *
     * @return bool Result
     */
    public function canBeDeleted(TodoList $todoList): bool
    {
        try {
            $result = $this->noteRepository->countByTodoList($todoList);

            return !($result > 0);
            // @codeCoverageIgnoreStart
        } catch (NoResultException|NonUniqueResultException) {
            return false;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Find by id.
     *
     * @param int $id TodoList id
     *
     * @return TodoList|null TodoList entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?TodoList
    {
        return $this->todoListRepository->findOneById($id);
    }
}
