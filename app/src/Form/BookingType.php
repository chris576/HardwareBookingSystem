<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Hardware;
use App\Repository\HardwareRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    private HardwareRepository $hardwareRepository;
    public function __construct(HardwareRepository $hardwareRepository) {
        $this->hardwareRepository = $hardwareRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hardware', ChoiceType::class,[
                'choices' => $this->hardwareRepository->findAll(),
                'choice_label' => function (Hardware $value, $key, $index) {
                    return $value->getName();
                },
            ])
            ->add('date', DateType::class, [
                'mapped' => false,
                'label' => 'Datum'
            ])
            ->add('startTime', TimeType::class, [
                'mapped' => false,
                'label' => 'Startzeit'
            ])
            ->add('endTime', TimeType::class, [
                'mapped' => false,
                'label' => 'Endzeit'
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}