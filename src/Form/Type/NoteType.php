<?php
/**
 * Note type.
 */

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Enum\NoteStatus;
use App\Entity\Note;
use App\Entity\TodoList;
use App\Entity\User;
use App\Repository\TodoListRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

/**
 * Class NoteType.
 */
class NoteType extends AbstractType
{
    /**
     * Security.
     */
    private Security $security;

    /**
     * TodoList service.
     */
    private TodoListRepository $todoListRepository;

    /**
     * Security.
     *
     * @param Security           $security           Security
     * @param TodoListRepository $todoListRepository TodoListRepository
     */
    public function __construct(Security $security, TodoListRepository $todoListRepository)
    {
        $this->security = $security;
        $this->todoListRepository = $todoListRepository;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $builder->add(
            'content',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );
        $builder->add(
            'status',
            EnumType::class,
            [
                'label' => 'label.status',
                'required' => true,
                'class' => NoteStatus::class,
            ]
        );
        $builder->add(
            'priority',
            RangeType::class,
            [
                'label' => 'label.priority',
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
            ]
        );
        $builder->add(
            'category',
            EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => function ($category): string {
                    return $category->getTitle();
                },
                'label' => 'label.category',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );
        $builder->add(
            'todoList',
            EntityType::class,
            [
                'class' => TodoList::class,
                'choice_label' => function ($todoList): string {
                    return $todoList->getTitle();
                },
                'choices' => $this->todoListRepository->queryByAuthor($user)->getQuery()->getResult(),
                'label' => 'label.todo_list',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Note::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'note';
    }
}
