<?php
/**
 * TodoList fixtures.
 */

namespace App\DataFixtures;

use App\Entity\TodoList;
use DateTimeImmutable;

/**
 * Class TodoListFixtures.
 */
class TodoListFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        $this->createMany(20, 'todoLists', function (int $i) {
            $todoList = new TodoList();
            $todoList->setTitle(ucfirst($this->faker->unique()->word) . ' ' . $this->faker->unique()->word);
            $todoList->setCreatedAt(
                DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $todoList->setUpdatedAt(
                DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            return $todoList;
        });

        $this->manager->flush();
    }
}
