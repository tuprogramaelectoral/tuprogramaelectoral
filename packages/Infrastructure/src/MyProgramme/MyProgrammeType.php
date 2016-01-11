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
            ->add('policies', 'text', [
                'constraints' => [new InterestsExist()]
            ])
            ->add('completed', 'choice', [
                'choices' => ['No' => false, 'Yes' => true],
                'choices_as_values' => true,
                'expanded' => true
            ])
            ->add('public', 'choice', array(
                'choices' => ['No' => false, 'Yes' => true],
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
        $forms['completed']->setData($data ? $data->isCompleted() : false);
        $forms['public']->setData($data ? $data->isPublic() : false);
    }

    public function mapFormsToData($forms, &$data)
    {
        /** @var MyProgramme $data */
        $forms = iterator_to_array($forms);
        $d['policies'] = $forms['policies']->getData();
        $d['completed'] = $forms['completed']->getData();
        $d['public'] = $forms['public']->getData();

        if (is_array($d['policies'])) {
            foreach ($d['policies'] as $interest => $policiy) {
                $data->selectPolicy($interest, $policiy);
            }
        }

        $data->setPublic(is_bool($d['public']) ? $d['public'] : false);
        $data->setCompleted(is_bool($d['completed']) ? $d['completed'] : false);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'empty_data' => null,
            'constraints' => [new MyProgrammeIsCompleted()]
        ));
    }

    public function getName()
    {
        return 'app_myprogramme';
    }
}
