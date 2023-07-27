<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use App\Entity\TodoList;
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
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial note.{id, content, createdAt, updatedAt, status, priority}',
                'partial category.{id, title}',
                'partial todoList.{id, title}',
            )
            ->join('note.category', 'category')
            ->join('note.todoList', 'todoList')
            ->orderBy('note.priority', 'DESC');
    }

    /**
     * Query records by status.
     *
     * @return QueryBuilder Query builder
     */
    public function queryByStatus(NoteStatus $status): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('note.status = :status')
            ->setParameter('status', $status);
    }

    /**
     * Query records by category.
     *
     * @return QueryBuilder Query builder
     */
    public function queryByCategory(Category $category): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('note.category = :category')
            ->setParameter('category', $category);
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
     * Query records by todo-list.
     *
     * @return QueryBuilder Query builder
     */
    public function queryByTodoList(TodoList $todoList): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('note.todoList = :todoList')
            ->setParameter('todoList', $todoList);
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
}
