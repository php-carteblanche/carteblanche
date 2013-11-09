<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace App;

use \CarteBlanche\CarteBlanche,
    \CarteBlanche\App\FrontController as CarteBlancheFrontController,
    \CarteBlanche\Interfaces\FrontControllerInterface as CarteBlancheFrontControllerInterface,
    \CarteBlanche\App\Container,
    \CarteBlanche\Exception\NotFoundException,
    \CarteBlanche\Interfaces\FrontControllerInterface;

/**
 * This class is you application custom FrontController inheriting from the default `\CarteBlanche\App\FrontController` 
 */
class FrontController
    extends CarteBlancheFrontController
    implements CarteBlancheFrontControllerInterface
{


}

// Endfile
