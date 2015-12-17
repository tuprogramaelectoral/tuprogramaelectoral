<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use TPE\Infraestructura\MiPrograma\MiProgramaType;

class DefaultController extends Controller
{
    public function healthCheckAction()
    {
        return new JsonResponse(['health check']);
    }
}
