<?php
/**
 * TodoList service tests.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\TodoList;
use App\Repository\CategoryRepository;
use App\Repository\TodoListRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;

/**
 * Class TodoListServiceTest.
 */
class TodoListServiceTest extends ServiceHelpers
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $todoList = new TodoList();
        $todoList->setTitle('todolist');
        $user = $this->createUser('todolist@test.com');
        $todoList->setAuthor($user);
        $todoList->setCreatedAt(new \DateTimeImmutable());
        $todoList->setUpdatedAt(new \DateTimeImmutable());

        // when
        $this->todoListService->save($todoList);

        // then
        $todoListId = $todoList->getId();

        /** @var TodoList $resultTodoList */
        $resultTodoList = $this->entityManager->createQueryBuilder()
            ->select('todo_list')
            ->from(TodoList::class, 'todo_list')
            ->where('todo_list.id = :id')
            ->setParameter(':id', $todoListId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($todoList, $resultTodoList);
        $this->assertEquals($todoList->getTitle(), $resultTodoList->getTitle());
        $this->assertEquals($todoList->getAuthor(), $resultTodoList->getAuthor());
        $this->assertEquals($todoList->getUpdatedAt(), $resultTodoList->getUpdatedAt());
        $this->assertEquals($todoList->getCreatedAt(), $resultTodoList->getCreatedAt());
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $todoList = new TodoList();
        $todoList->setTitle('todolist');
        $user = $this->createUser('todolist1@test.com');
        $todoList->setAuthor($user);

        // when
        $this->todoListService->delete($todoList);

        // then
        $todoListId = $todoList->getId();

        /** @var TodoList $resultTodoList */
        $resultTodoList = $this->entityManager->createQueryBuilder()
            ->select('todo_list')
            ->from(Category::class, 'todo_list')
            ->where('todo_list.id = :id')
            ->setParameter(':id', $todoListId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultTodoList);
    }

    /**
     * Test get paginated list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $records = TodoListRepository::PAGINATOR_ITEMS_PER_PAGE;
        $user = $this->createUser('todolist2@test.com');

        for ($i = 0; $i < 20; $i++) {
            $this->createTodoList('todolist ' . $i, $user);
        }

        // when
        $result = $this->todoListService->getPaginatedList($page, $user);

        // then
        $this->assertEquals($records, $result->count());
    }

    /**
     * Test todolist find one by id.
     *
     * @throws NonUniqueResultException
     */
    public function testFindOneById(): void
    {
        // given
        $todoList = new TodoList();
        $todoList->setTitle('test TodoList');
        $this->todoListService->save($todoList);
        $todoListId = $todoList->getId();

        // when
        $resultToDoListId = $this->todoListService->findOneById($todoListId)->getId();

        // then
        $this->assertEquals($todoListId, $resultToDoListId);
    }

    /**
     * Test todolist can be deleted.
     */
    public function testCanBeDeleted(): void
    {
        // given
        $user = $this->createUser('email@todolist.pl');
        $todoList = $this->createTodoList('todolist can be deleted', $user);
        $category = $this->createCategory('category for test');
        $categoryId = $category->getId();
        $note = $this->createNote($category, $todoList);

        // when
        $canBeDeleted = $this->todoListService->canBeDeleted($todoList);

        // then
        $this->assertEquals(false, $canBeDeleted);
    }

    /**
     * Test category can be deleted with exception.
     * @throws NonUniqueResultException
     */
    public function testCanBeDeletedWithException(): void
    {
        // then
        $this->expectException(NoResultException::class);

        // given
        /** @var TodoList $todoList */
        $todoList = $this->entityManager->createQueryBuilder()
            ->select('todo_list')
            ->from(TodoList::class, 'todo_list')
            ->where('todo_list.id = :id')
            ->setParameter(':id', 122222, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        // when
        $canBeDeleted = $this->todoListService->canBeDeleted($todoList);
        $this->assertEquals(false, $canBeDeleted);
    }
}
