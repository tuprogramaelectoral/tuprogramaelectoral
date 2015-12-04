<?php

namespace TPE\Infraestructura\MiPrograma;

use Symfony\Component\Validator\Constraint;


class MiProgramaEstaTerminado extends Constraint
{
    public $message = 'Quedan políticas por asignar al programa';

    public function validatedBy()
    {
        return 'mi_programa_esta_terminado_validador';
    }
}
