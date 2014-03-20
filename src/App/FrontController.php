<?php

namespace App;

use \CarteBlanche\CarteBlanche,
    \CarteBlanche\App\FrontController as CarteBlancheFrontController,
    \CarteBlanche\Interfaces\FrontControllerInterface as CarteBlancheFrontControllerInterface,
    \CarteBlanche\App\Container,
    \CarteBlanche\Exception\NotFoundException,
    \CarteBlanche\Interfaces\FrontControllerInterface;

/**
 * This class is you application custom FrontController
 * inheriting from the default `\CarteBlanche\App\FrontController` 
 */
class FrontController
    extends CarteBlancheFrontController
    implements CarteBlancheFrontControllerInterface
{


}

// Endfile
