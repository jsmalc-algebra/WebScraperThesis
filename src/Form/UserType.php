<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{

    public function __construct(private readonly Security $security){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('plainPassword', PasswordType::class,[
                'always_empty' => true,
                'mapped' => false,
                'required' => $options['require_password'],
                'constraints' => $options['require_password']
                    ? [new NotBlank(message: 'Password is required.')]
                    : [],
            ]);

        if ($this->security->isGranted('ROLE_ADMIN')){
            $builder
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                        'Premium' => 'ROLE_PREMIUM',
                        'Free' => 'ROLE_FREE',
                    ],
                    'multiple' => false,
                    'expanded' => true,
                ]);

            $builder->get('roles')->addModelTransformer(new CallbackTransformer(
                fn (?array $rolesArray) => $rolesArray[0] ?? null,
                fn (?string $roleString) => $roleString ? [$roleString] : []
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'require_password' => true
        ]);
    }
}
