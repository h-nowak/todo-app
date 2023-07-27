<?php

namespace App\Controller;

use App\Entity\Enum\TodoListStatus;
use App\Entity\TodoList;
use App\Service\TodoListServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo-lists')]
class TodoListController extends AbstractController
{
    /**
     * TodoList service.
     */
    private TodoListServiceInterface $todoListService;

    /**
     * Constructor.
     */
    public function __construct(TodoListServiceInterface $todoListService)
    {
        $this->todoListService = $todoListService;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'todoList_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->todoListService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('todo_list/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param TodoList    $todoList    TodoList
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/',
        name: 'todoList_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(TodoList $todoList): Response
    {
        return $this->render(
            'todo_list/show.html.twig',
            ['todoList' => $todoList]
        );
    }
}