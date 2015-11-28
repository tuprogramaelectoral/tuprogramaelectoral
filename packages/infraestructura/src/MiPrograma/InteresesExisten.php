<?php

namespace TPE\Infraestructura\MiPrograma;

use Symfony\Component\Validator\Constraint;


class InteresesExisten extends Constraint
{
    public $message = 'Algunos de los intereses o políticas no existen en el sistema';

    public function validatedBy()
    {
        return 'intereses_existen_validador';
    }
}
