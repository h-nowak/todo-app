<?php
/**
 * Note role.
 */

namespace App\Entity\Enum;

/**
 * Enum NoteStatus.
 */
enum NoteStatus: string
{
    case STATUS_TODO = 'STATUS_TODO';
    case STATUS_IN_PROGRESS= 'STATUS_IN_PROGRESS';
    case STATUS_DONE= 'STATUS_DONE';


    /**
     * Get the role label.
     *
     * @return string Role label
     */
    public function label(): string
    {
        return match ($this) {
            NoteStatus::STATUS_TODO => 'label.status_todo',
            NoteStatus::STATUS_IN_PROGRESS => 'label.status_in_progress',
            NoteStatus::STATUS_DONE => 'label.status_done',
        };
    }
}
