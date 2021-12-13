<?php

namespace App\Form;

use App\Entity\Command;
use Symfony\Component\Form\AbstractType;
use App\Form\ProductFormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client_fullname')
            ->add('address')
            ->add('phone')
            ->add('limit_date')
            ->add('products', CollectionType::class, [
                'entry_type' => ProductFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
