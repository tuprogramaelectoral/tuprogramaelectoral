<?php

namespace TPE\Infraestructura\MiPrograma;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TPE\Dominio\MiPrograma\MiPrograma;


class MiProgramaType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'politicas',
                'text',
                [
                    'constraints' => [
                        new InteresesExisten(),
                    ]
                ])
            ->setDataMapper($this);
    }

    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);
        $forms['politicas']->setData($data ? $data->getPoliticas() : []);
    }

    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);
        $politicas = $forms['politicas']->getData();
        if (is_array($politicas)) {
            foreach ($politicas as $interes => $politica) {
                $data->elegirPolitica($interes, $politica);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'empty_data' => null,
        ));
    }

    public function getName()
    {
        return 'app_miprograma';
    }
}
