<?php
/**
 * Note fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use DateTimeImmutable;

/**
 * Note fixtures class.
 */
class NoteFixtures extends AbstractBaseFixtures
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
        for ($i = 0; $i < 100; ++$i) {
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
            $this->manager->persist($note);
        }

        $this->manager->flush();
    }
}
