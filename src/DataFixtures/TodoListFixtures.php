<?php
/**
 * TodoList fixtures.
 */

namespace App\DataFixtures;

use App\Entity\TodoList;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class TodoListFixtures.
 */
class TodoListFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        $this->createMany(20, 'todoLists', function (int $i) {
            $todoList = new TodoList();
            $todoList->setTitle(ucfirst($this->faker->unique()->word).' '.$this->faker->unique()->word);
            $todoList->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $todoList->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            /** @var User $author */
            $author = $this->getRandomReference('admins');
            $todoList->setAuthor($author);

            return $todoList;
        });

        $this->manager->flush();
    }

    /**
     * Get dependencies.
     *
     * @return string[] of dependencies
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
