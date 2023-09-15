<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Hardware;
use App\Repository\BookingRepository;
use App\Repository\HardwareRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

class BookingType extends AbstractType
{
    private HardwareRepository $hardwareRepository;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(HardwareRepository $hardwareRepository, BookingRepository $bookingRepository, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->hardwareRepository = $hardwareRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hardware', ChoiceType::class, [
                'choices' => $this->hardwareRepository->findAll(),
                'choice_label' => function (Hardware $value, $key, $index) {
                    return $value->getName();
                },
            ])
            ->add('startDateTime', DateTimeType::class, [
                'mapped' => false,
                'with_minutes' => false,
                'label' => 'Startzeit'
            ]);
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->add('length', NumberType::class, [
                'mapped' => false,
                'label' => false,
                'scale' => 0,
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Der Wert muss größer als 0 sein.',
                    ]),
                ],
            ]);
        }
        $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
