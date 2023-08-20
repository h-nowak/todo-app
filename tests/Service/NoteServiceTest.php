<?php
/**
 * Note service tests.
 */

namespace App\Tests\Service;

use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;


/**
 * Class NoteServiceTest.
 */
class NoteServiceTest extends ServiceHelpers
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
        $category = $this->createCategory('category for note');
        $user = $this->createUser('user@user.pl');
        $todoList = $this->createTodoList('todolist for note', $user);
        $note = new Note();
        $note->setPriority(4);
        $note->setTodoList($todoList);
        $note->setStatus(NoteStatus::STATUS_DONE);
        $note->setContent('abc');
        $note->setCategory($category);
        $note->setCreatedAt(new \DateTimeImmutable());
        $note->setUpdatedAt(new \DateTimeImmutable());

        // when
        $this->noteService->save($note);

        // then
        $noteId = $note->getId();

        /** @var Note $resultNote */
        $resultNote = $this->entityManager->createQueryBuilder()
            ->select('note')
            ->from(Note::class, 'note')
            ->where('note.id = :id')
            ->setParameter(':id', $noteId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($note, $resultNote);
        $this->assertEquals($note->getContent(), $resultNote->getContent());
        $this->assertEquals($note->getTodoList(), $resultNote->getTodoList());
        $this->assertEquals($note->getCategory(), $resultNote->getCategory());
        $this->assertEquals($note->getId(), $resultNote->getId());
        $this->assertEquals($note->getPriority(), $resultNote->getPriority());
        $this->assertEquals($note->getStatus(), $resultNote->getStatus());
        $this->assertEquals($note->getCreatedAt(), $resultNote->getCreatedAt());
        $this->assertEquals($note->getUpdatedAt(), $resultNote->getUpdatedAt());
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $category = $this->createCategory('category for note delete');
        $user = $this->createUser('user@userdelete.pl');
        $todoList = $this->createTodoList('todolist for note delete', $user);
        $note = $this->createNote($category, $todoList);
        $noteId = $note->getId();

        // when
        $this->noteService->delete($note);

        // then
        /** @var Note $resultNote */
        $resultNote = $this->entityManager->createQueryBuilder()
            ->select('note')
            ->from(Note::class, 'note')
            ->where('note.id = :id')
            ->setParameter(':id', $noteId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultNote);
    }

    /**
     * Test get paginated list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $records = NoteRepository::PAGINATOR_ITEMS_PER_PAGE;
        $category = $this->createCategory('category for note pagination');
        $user = $this->createUser('user@pagination.pl');
        $todoList = $this->createTodoList('todolist for note pagination', $user);

        for ($i = 0; $i < 100; $i++) {
            $note = $this->createNote($category, $todoList);
        }

        // when
        $result = $this->noteService->getPaginatedList($page);

        // then
        $this->assertEquals($records, $result->count());
    }

    /**
     * Test get paginated list by status.
     *
     * @throws NonUniqueResultException
     */
    public function testGetPaginatedListByStatus(): void
    {
        // given
        $page = 1;
        $records = NoteRepository::PAGINATOR_ITEMS_PER_PAGE;
        $category = $this->createCategory('category for note pagination');
        $categoryId = $category->getId();
        $user = $this->createUser('user@pagination.pl');
        $todoList = $this->createTodoList('todolist for note pagination', $user);
        $todoListId = $todoList->getId();
        $filters = [
            'category_id' => $categoryId,
            'todolist_id' => $todoListId,
        ];

        for ($i = 0; $i < 100; $i++) {
            $note = $this->createNote($category, $todoList);
        }

        // when
        $result = $this->noteService->getPaginatedListByStatus($page, NoteStatus::STATUS_DONE, $user, $filters);

        // then
        $this->assertEquals($records, $result->count());
    }
}
