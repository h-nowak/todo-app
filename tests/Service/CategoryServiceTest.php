<?php
/**
 * Category service tests.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class CategoryServiceTest.
 */
class CategoryServiceTest extends ServiceHelpers
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
        $category = new Category();
        $category->setTitle('category');
        $category->setCreatedAt(new \DateTimeImmutable());
        $category->setUpdatedAt(new \DateTimeImmutable());

        // when
        $this->categoryService->save($category);

        // then
        $categoryId = $category->getId();

        /** @var Category $resultCategory */
        $resultCategory = $this->entityManager->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->where('category.id = :id')
            ->setParameter(':id', $categoryId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($category, $resultCategory);
        $this->assertEquals($category->getTitle(), $resultCategory->getTitle());
        $this->assertEquals($category->getCreatedAt(), $resultCategory->getCreatedAt());
        $this->assertEquals($category->getUpdatedAt(), $resultCategory->getUpdatedAt());
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $category = new Category();
        $category->setTitle('category');

        // when
        $this->categoryService->delete($category);

        // then
        $categoryId = $category->getId();

        /** @var Category $resultCategory */
        $resultCategory = $this->entityManager->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->where('category.id = :id')
            ->setParameter(':id', $categoryId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultCategory);
    }

    /**
     * Test get paginated list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $records = CategoryRepository::PAGINATOR_ITEMS_PER_PAGE;

        for ($i = 0; $i < 100; $i++) {
            $category = $this->createCategory('category ' . $i);
        }

        // when
        $result = $this->categoryService->getPaginatedList($page);

        // then
        $this->assertEquals($records, $result->count());
    }

    /**
     * Test category find one by id.
     *
     * @throws NonUniqueResultException
     */
    public function testFindOneById(): void
    {
        // given
        $category = new Category();
        $category->setTitle('testCategory');
        $this->categoryService->save($category);
        $categoryId = $category->getId();

        // when
        $resultCategoryId = $this->categoryService->findOneById($categoryId)->getId();

        // then
        $this->assertEquals($categoryId, $resultCategoryId);
    }

    /**
     * Test category can be deleted.
     */
    public function testCanBeDeleted(): void
    {
        // given
        $category = new Category();
        $category->setTitle('testCategoryTitle2');
        $this->categoryService->save($category);
        $user = $this->createUser('user@category.com');
        $todoList = $this->createTodoList('category can be deleted', $user);
        $categoryId = $category->getId();
        $user = $this->createUser('email@category.pl');
        $note = $this->createNote($category, $todoList);

        // when
        $canBeDeleted = $this->categoryService->canBeDeleted($category);

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
        /** @var Category $resultCategory */
        $category = $this->entityManager->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->where('category.id = :id')
            ->setParameter(':id', 123123123, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        // when
        $canBeDeleted = $this->categoryService->canBeDeleted($category);
        $this->assertEquals(false, $canBeDeleted);
    }
}
