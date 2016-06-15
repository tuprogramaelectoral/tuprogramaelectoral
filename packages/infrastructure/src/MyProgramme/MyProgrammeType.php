<?php

namespace TPE\Infrastructure\MyProgramme;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TPE\Domain\MyProgramme\MyProgramme;


class MyProgrammeType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('policies', 'text')
            ->add('edition', 'text')
            ->add('completed', 'choice', [
                'choices' => ['No', 'Yes'],
                'choices_as_values' => true,
                'expanded' => true
            ])
            ->add('public', 'choice', array(
                'choices' => ['No', 'Yes'],
                'choices_as_values' => true,
                'expanded' => true
            ))
            ->setDataMapper($this);
    }

    public function mapDataToForms($data, $forms)
    {
        /** @var MyProgramme $data */
        $forms = iterator_to_array($forms);
        $forms['policies']->setData($data ? $data->getpolicies() : []);
        $forms['edition']->setData($data ? $data->getEdition() : 1);
        $forms['completed']->setData($data ? (($data->isCompleted()) ? 'Yes' : 'No') : 'No');
        $forms['public']->setData($data ? (($data->isPublic()) ? 'Yes' : 'No') : 'No');
    }

    public function mapFormsToData($forms, &$data)
    {
        /** @var MyProgramme $data */
        $forms = iterator_to_array($forms);
        $d['policies'] = $forms['policies']->getData();
        $d['edition'] = $forms['edition']->getData();
        $d['completed'] = $forms['completed']->getData();
        $d['public'] = $forms['public']->getData();

        if (is_array($d['policies'])) {
            foreach ($d['policies'] as $interest => $policiy) {
                $data->selectPolicy($interest, $policiy);
            }
        }

        if (!empty($d['edition'])) {
            $data->setEdition($d['edition']);
        }

        $data->setPublic(('Yes' === $d['public']) ? true : false);
        $data->setCompleted(('Yes' === $d['completed']) ? true : false);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'empty_data' => null,
            'constraints' => [new MyProgrammeIsCompleted(), new InterestsExist()]
        ));
    }

    public function getName()
    {
        return 'app_myprogramme';
    }
}
