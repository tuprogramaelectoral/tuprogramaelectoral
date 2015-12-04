<?php

namespace TPE\Infraestructura\MiPrograma;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TPE\Dominio\MiPrograma\MiPrograma;


class MiProgramaEstaTerminadoValidator extends ConstraintValidator
{
    public function validate($miPrograma, Constraint $constraint)
    {
        /** @var MiPrograma $miPrograma */
        if ($miPrograma->isTerminado()) {
            $faltan = false;
            foreach ($miPrograma->getIntereses() as $interes) {
                if (null === $miPrograma->getPolitica($interes)) {
                    $faltan = true;
                }
            }

            if ($faltan) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
