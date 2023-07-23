<?php

namespace App\Controller;

use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use App\Service\NoteServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notes')]
class NoteController extends AbstractController
{
    /**
     * Note service.
     */
    private NoteServiceInterface $noteService;

    /**
     * Constructor.
     */
    public function __construct(NoteServiceInterface $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'note_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $statusCases = NoteStatus::cases();

        foreach($statusCases as $status) {
            $paginations[] = $this->noteService->getPaginatedListByStatus(
                $request->query->getInt('page', 1),
                $status,
            );
        }

        return $this->render('note/index.html.twig', ['paginations' => $paginations, 'statusCases' => $statusCases]);
    }

    /**
     * Show action.
     *
     * @param Note    $note    Note
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/',
        name: 'note_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Note $note): Response
    {
        return $this->render(
            'note/show.html.twig',
            ['note' => $note]
        );
    }
}