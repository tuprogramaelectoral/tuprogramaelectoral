<?php

namespace TPE\Infraestructura\MiPrograma;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TPE\Infraestructura\Ambito\AmbitoBaseDeDatosRepositorio;

class InteresesExistenValidator extends ConstraintValidator
{
    /**
     * @var MiProgramaBaseDeDatosRepositorio
     */
    private $repositorio;


    public function __construct(MiProgramaBaseDeDatosRepositorio $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    public function validate($values, Constraint $constraint)
    {
        if (is_array($values)) {
            $intereses = [];
            $politicas = [];
            foreach ($values as $interes => $politica) {
                if (!empty($interes)) {
                    $intereses[] = $interes;
                }
                if (!empty($politica)) {
                    $politicas[] = $politica;
                }
            }

            if (is_array($intereses) && !$this->repositorio->existenLosIntereses($intereses)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }

            if (is_array($politicas) && !$this->repositorio->existenLasPoliticas($politicas)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
