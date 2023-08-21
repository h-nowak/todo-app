<?php
/**
 * Note repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use App\Entity\TodoList;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 30;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    /**
     * Query all records.
     *
     * @param array<string, object> $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(array $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial note.{id, content, createdAt, updatedAt, status, priority}',
                'partial category.{id, title}',
                'partial todoList.{id, title}',
            )
            ->join('note.category', 'category')
            ->join('note.todoList', 'todoList')
            ->orderBy('note.priority', 'DESC');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Query all records.
     *
     * @param User  $author  User
     * @param array $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryByUser(User $author, array $filters = []): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        $queryBuilder->andWhere('todoList.author = :author')
            ->setParameter('author', $author);

        return $queryBuilder;
    }

    /**
     * Query records by status.
     *
     * @param NoteStatus $status  Status
     * @param User       $author  User
     * @param array      $filters Filters
     *
     * @return QueryBuilder QueryBuilder
     */
    public function queryByStatus(NoteStatus $status, User $author, array $filters = []): QueryBuilder
    {
        return $this->queryByUser($author, $filters)
            ->andWhere('note.status = :status')
            ->setParameter('status', $status);
    }

    /**
     * Count notes by category.
     *
     * @param Category $category Category
     *
     * @return int Number of notes in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('note.id'))
            ->where('note.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count notes by todo-list.
     *
     * @param TodoList $todoList Todo-list
     *
     * @return int Number of notes in todo-list
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByTodoList(TodoList $todoList): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('note.id'))
            ->where('note.todoList = :todoList')
            ->setParameter('todoList', $todoList)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Save entity.
     *
     * @param Note $note Note entity
     */
    public function save(Note $note): void
    {
        $this->_em->persist($note);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Note $note Note entity
     */
    public function delete(Note $note): void
    {
        $this->_em->remove($note);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('note');
    }

    /**
     * Apply filters to paginated list.
     *
     * @param QueryBuilder          $queryBuilder Query builder
     * @param array<string, object> $filters      Filters array
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, array $filters = []): QueryBuilder
    {
        if (isset($filters['category']) && $filters['category'] instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters['category']);
        }

        if (isset($filters['todolist']) && $filters['todolist'] instanceof TodoList) {
            $queryBuilder->andWhere('todoList = :todolist')
                ->setParameter('todolist', $filters['todolist']);
        }

        return $queryBuilder;
    }
}
