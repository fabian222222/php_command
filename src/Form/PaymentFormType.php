<?php

namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Regex;


class PaymentFormType extends AbstractType
{
    /**/
    /**
     * @Regex("[(\d) ]*")
    */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    "cheque" => "cheque",
                    "cb" => "cb",
                    "8 fois" => "8 fois",
                ]
            ])
            ->add('amount', null, [
                'constraints' => array(new Regex("[(\d)]"))
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
