<?php

declare(strict_types=1);

namespace App\Form\Mx;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class QueryType extends AbstractType
{
    final public const ITEM_QUERY = 'ITEM_QUERY';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::ITEM_QUERY, TextType::class, [
                'label'      => 'Query',
            ])
            ->add('run', SubmitType::class, [
                'label'      => 'Run',
            ])
        ;
    }
}
