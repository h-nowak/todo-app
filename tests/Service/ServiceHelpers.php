<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use App\Entity\TodoList;
use App\Entity\User;
use App\Service\CategoryService;
use App\Service\CategoryServiceInterface;
use App\Service\NoteService;
use App\Service\NoteServiceInterface;
use App\Service\TodoListService;
use App\Service\TodoListServiceInterface;
use App\Service\UserService;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class ServiceHelpers extends KernelTestCase
{
    /**
     * Repository.
     */
    protected ?EntityManagerInterface $entityManager;

    /**
     * Category service.
     */
    protected ?CategoryServiceInterface $categoryService;

    /**
     * Note service.
     */
    protected ?NoteServiceInterface $noteService;

    /**
     * TodoList service.
     */
    protected ?TodoListServiceInterface $todoListService;

    /**
     * User service.
     */
    protected ?UserServiceInterface $userService;

    /**
     * Password hasher.
     *
     * @var UserPasswordHasherInterface|null
     */
    protected ?UserPasswordHasherInterface $hasher;

    /**
     * Set up.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->categoryService = $container->get(CategoryService::class);
        $this->noteService = $container->get(NoteService::class);
        $this->todoListService = $container->get(TodoListService::class);
        $this->userService = $container->get(UserService::class);
        $this->hasher = $container->get(UserPasswordHasherInterface::class);
    }

    /**
     * Helper create note.
     *
     * @param Category $category Category
     * @param TodoList $todoList Todolist
     *
     * @return Note
     */
    protected function createNote(Category $category, TodoList $todoList): Note
    {
        $note = new Note();
        $note->setCategory($category);
        $note->setContent('Content');
        $note->setStatus(NoteStatus::STATUS_DONE);
        $note->setTodoList($todoList);
        $note->setPriority(5);

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return $note;
    }

    /**
     * Create user helper.
     *
     * @param string $email
     *
     * @return User User
     */
    protected function createUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('1234');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Create todolist helper.
     *
     * @param string $name Name
     * @param User   $user User
     *
     * @return TodoList Todolist
     */
    protected function createTodoList(string $name, User $user): TodoList
    {
        $todolist = new TodoList();
        $todolist->setTitle($name);
        $todolist->setAuthor($user);
        $this->entityManager->persist($todolist);
        $this->entityManager->flush();

        return $todolist;
    }

    /**
     * Create category helper.
     *
     * @param string $name Name
     *
     * @return Category Category
     */
    protected function createCategory(string $name): Category
    {
        $category = new Category();
        $category->setTitle($name);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }
}