<?php
/**
 * Note service.
 */

namespace App\Service;

use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use App\Entity\User;
use App\Repository\NoteRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class NoteService.
 */
class NoteService implements NoteServiceInterface
{
    /**
     * Note repository.
     */
    private NoteRepository $noteRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * CategoryServiceInterface.
     */
    private CategoryServiceInterface $categoryService;

    /**
     * TodoListServiceInterface.
     */
    private TodoListServiceInterface $todoListService;

    /**
     * Constructor.
     *
     * @param NoteRepository           $noteRepository  Note repository
     * @param PaginatorInterface       $paginator       Paginator
     * @param CategoryServiceInterface $categoryService Category service
     * @param TodoListServiceInterface $todoListService TodoList service
     */
    public function __construct(NoteRepository $noteRepository, PaginatorInterface $paginator, CategoryServiceInterface $categoryService, TodoListServiceInterface $todoListService)
    {
        $this->noteRepository = $noteRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
        $this->todoListService = $todoListService;
    }

    /**
     * Get paginated list.
     *
     * @param int   $page    Page
     * @param array $filters Filters
     *
     * @return PaginationInterface Pagination
     */
    public function getPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->noteRepository->queryAll($filters),
            $page,
            NoteRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list by status.
     *
     * @param int        $page    Page number
     * @param NoteStatus $status  Status
     * @param User       $author  Author
     * @param array      $filters Filters
     *
     * @return PaginationInterface Paginated list
     *
     * @throws NonUniqueResultException Exception
     */
    public function getPaginatedListByStatus(int $page, NoteStatus $status, User $author, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->noteRepository->queryByStatus($status, $author, $filters),
            $page,
            NoteRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Note $note Note entity
     */
    public function save(Note $note): void
    {
        $this->noteRepository->save($note);
    }

    /**
     * Delete entity.
     *
     * @param Note $note Note entity
     */
    public function delete(Note $note): void
    {
        $this->noteRepository->delete($note);
    }

    /**
     * Prepare filters for the tasks list.
     *
     * @param array $filters Raw filters from request
     *
     * @return array Result array of filters
     *
     * @throws NonUniqueResultException Exception
     */
    private function prepareFilters(array $filters): array
    {
        $resultFilters = [];
        if (!empty($filters['category_id'])) {
            $category = $this->categoryService->findOneById($filters['category_id']);
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        if (!empty($filters['todolist_id'])) {
            $todolist = $this->todoListService->findOneById($filters['todolist_id']);
            if (null !== $todolist) {
                $resultFilters['todolist'] = $todolist;
            }
        }

        return $resultFilters;
    }
}
