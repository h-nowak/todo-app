<?php
/**
 * TodoList controller.
 */

namespace App\Controller;

use App\Entity\TodoList;
use App\Entity\User;
use App\Form\Type\TodoListType;
use App\Service\TodoListServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * TodoListController class.
 */
#[Route('/todo-lists')]
class TodoListController extends AbstractController
{
    /**
     * TodoList service.
     *
     * @var TodoListServiceInterface TodoList service
     */
    private TodoListServiceInterface $todoListService;
    /**
     * Translator.
     *
     * @var TranslatorInterface Translator
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TodoListServiceInterface $todoListService Service
     * @param TranslatorInterface      $translator      Translator
     */
    public function __construct(TodoListServiceInterface $todoListService, TranslatorInterface $translator)
    {
        $this->todoListService = $todoListService;
        $this->translator = $translator;
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
        /** @var User $user */
        $user = $this->getUser();

        $pagination = $this->todoListService->getPaginatedList(
            $request->query->getInt('page', 1),
            $user,
        );

        return $this->render('todo_list/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'todoList_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $todoList = new TodoList();
        $todoList->setAuthor($this->getUser());
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoListService->save($todoList);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('todoList_index');
        }

        return $this->render(
            'todo_list/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param TodoList $todoList TodoList entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'todoList_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'todoList')]
    public function edit(Request $request, TodoList $todoList): Response
    {
        $form = $this->createForm(
            TodoListType::class,
            $todoList,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('todoList_edit', ['id' => $todoList->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoListService->save($todoList);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('todoList_index');
        }

        return $this->render(
            'todo_list/edit.html.twig',
            [
                'form' => $form->createView(),
                'todoList' => $todoList,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param TodoList $todoList TodoList entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'todoList_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'todoList')]
    public function delete(Request $request, TodoList $todoList): Response
    {
        if (!$this->todoListService->canBeDeleted($todoList)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.todoList_contains_tasks')
            );

            return $this->redirectToRoute('todoList_index');
        }

        $form = $this->createForm(
            FormType::class,
            $todoList,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('todoList_delete', ['id' => $todoList->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->todoListService->delete($todoList);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('todoList_index');
        }

        return $this->render(
            'todo_list/delete.html.twig',
            [
                'form' => $form->createView(),
                'todoList' => $todoList,
            ]
        );
    }

    /**
     * Show action.
     *
     * @param TodoList $todoList TodoList
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/',
        name: 'todoList_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[IsGranted('VIEW', subject: 'todoList')]
    public function show(TodoList $todoList): Response
    {
        return $this->render(
            'todo_list/show.html.twig',
            ['todoList' => $todoList]
        );
    }
}
