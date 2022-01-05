<?php

declare(strict_types=1);

namespace App\Form\InclusionUpdate;

use App\Utils\Artisan\SmartAccessDecorator as Artisan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notes', TextareaType::class, [
                'label'      => 'Anything else? ("notes")',
                'help'       => '<strong>WARNING!</strong> This is information 1) will <strong>NOT</strong> be visible on getfursu.it, yet it 2) <strong>WILL</strong> however be public. Treat this as place for comments for getfursu.it maintainer or some additional information which might be added to the website in the future.',
                'help_html'  => true,
                'required'   => false,
                'empty_data' => '',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'iu_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'        => Artisan::class,
        ]);
    }
}
