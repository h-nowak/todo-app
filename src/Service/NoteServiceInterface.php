<?php
/**
 * Note service interface.
 */

namespace App\Service;

use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class NoteServiceInterface.
 */
interface NoteServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list by status.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByStatus(int $page, NoteStatus $status): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Note $note Note entity
     */
    public function save(Note $note): void;

    /**
     * Delete entity.
     *
     * @param Note $note Note entity
     */
    public function delete(Note $note): void;
}
