<?php
/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Exceptions;

use Exception;

/**
 * Třída rodičovské vyjímky od které dědí všechny ostatní vyjímky aplikace
 */
class BaseException extends Exception{
    // poznámka: zde by bylo zpracováno např. logování výjímek a podobně
}
