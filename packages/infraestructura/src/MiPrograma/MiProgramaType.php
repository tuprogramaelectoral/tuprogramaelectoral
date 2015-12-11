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
            ->add('politicas', 'text', [
                'constraints' => [new InteresesExisten()]
            ])
            ->add('terminado', 'choice', [
                'choices' => ['No' => false, 'Si' => true],
                'choices_as_values' => true,
                'expanded' => true
            ])
            ->add('publico', 'choice', array(
                'choices' => ['No' => false, 'Si' => true],
                'choices_as_values' => true,
                'expanded' => true
            ))
            ->setDataMapper($this);
    }

    public function mapDataToForms($data, $forms)
    {
        /** @var MiPrograma $data */
        $forms = iterator_to_array($forms);
        $forms['politicas']->setData($data ? $data->getPoliticas() : []);
        $forms['terminado']->setData($data ? $data->isTerminado() : false);
        $forms['publico']->setData($data ? $data->isPublico() : false);
    }

    public function mapFormsToData($forms, &$data)
    {
        /** @var MiPrograma $data */
        $forms = iterator_to_array($forms);
        $d['politicas'] = $forms['politicas']->getData();
        $d['publico'] = $forms['publico']->getData();
        $d['terminado'] = $forms['terminado']->getData();

        if (is_array($d['politicas'])) {
            foreach ($d['politicas'] as $interes => $politica) {
                $data->elegirPolitica($interes, $politica);
            }
        }

        $data->setPublico(is_bool($d['publico']) ? $d['publico'] : false);
        $data->setTerminado(is_bool($d['terminado']) ? $d['terminado'] : false);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'empty_data' => null,
            'constraints' => [new MiProgramaEstaTerminado()]
        ));
    }

    public function getName()
    {
        return 'app_miprograma';
    }
}
