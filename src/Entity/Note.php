<?php
/**
 * Note entity.
 */

namespace App\Entity;

use App\Entity\Enum\NoteStatus;
use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Note class.
 */
#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\Table(name: 'notes')]
class Note
{
    /**
     * Id.
     *
     * @var int|null Id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Note content.
     *
     * @var string|null Content
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private ?string $content = null;

    /**
     * Priority.
     *
     * @var int|null Priority
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $priority = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable|null Created at
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     *
     * @var \DateTimeImmutable|null Updated at
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Status.
     *
     * @var NoteStatus|null Status
     */
    #[ORM\Column(length: 20)]
    private NoteStatus|null $status = null;

    /**
     * Category.
     *
     * @var Category|null Category
     */
    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Category $category = null;

    /**
     * Todo list.
     *
     * @var TodoList|null TodoList
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?TodoList $todoList = null;

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for content.
     *
     * @return string|null Content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string|null $content Content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * Getter for priority.
     *
     * @return int|null Priority
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Setter for priority.
     *
     * @param int|null $priority Priority
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Getter for created at.
     *
     * @return \DateTimeImmutable|null Created at
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param \DateTimeImmutable|null $createdAt Created at
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for updated at.
     *
     * @return \DateTimeImmutable|null Updated at
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param \DateTimeImmutable|null $updatedAt Updated at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for status.
     *
     * @return NoteStatus|null Status
     */
    public function getStatus(): ?NoteStatus
    {
        return $this->status;
    }

    /**
     * Setter for status.
     *
     * @param NoteStatus|null $status Status
     */
    public function setStatus(?NoteStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Getter for category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * Getter for TodoList.
     *
     * @return TodoList|null Todolist
     */
    public function getTodoList(): ?TodoList
    {
        return $this->todoList;
    }

    /**
     * Setter for TodoList.
     *
     * @param TodoList|null $todoList TodoList
     */
    public function setTodoList(?TodoList $todoList): void
    {
        $this->todoList = $todoList;
    }
}
