<?php

namespace App;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\FrontController as CarteBlancheFrontController;
use \CarteBlanche\Interfaces\FrontControllerInterface as CarteBlancheFrontControllerInterface;
use \CarteBlanche\App\Container;
use \CarteBlanche\Exception\NotFoundException;
use \CarteBlanche\Interfaces\FrontControllerInterface;

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
