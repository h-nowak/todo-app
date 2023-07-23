<?php
/**
 * Note fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Note fixtures class.
 */
class NoteFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Possible status.
     *
     * @var array $possibleStatus Possible status
     */
    private array $possibleStatus = [
        NoteStatus::STATUS_TODO,
        NoteStatus::STATUS_IN_PROGRESS,
        NoteStatus::STATUS_DONE,
    ];

    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'notes', function (int $i) {
            $note = new Note();
            $note->setContent($this->faker->sentences(1, true));
            $note->setPriority($this->faker->numberBetween(1,5));
            $note->setStatus($this->possibleStatus[$this->faker->numberBetween(0,2)]);
            $note->setCreatedAt(
                DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $note->setUpdatedAt(
                DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );

            /** @var Category $category */
            $category = $this->getRandomReference('categories');
            $note->setCategory($category);

            return $note;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
