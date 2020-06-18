<?php

/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 18.6.2020
 */

namespace Tests\App\Model\Facades;

use App\Model\Facades\VillageFacade;
use Tests\App\Model\Facades\AbstractFacadeTest;

require __DIR__."/../../app/bootstrap.php";

/**
 * Kostra testu fasády
 */
class VillageFacadeTest extends AbstractFacadeTest{
    
    /**
     * @return VillageFacade Reference na fasádu pro tento test
     */
    protected function getFacade(): VillageFacade {
        return new VillageFacade();
    }
    
    // Poznámka: Zde by byli testy pro danou fasádu. Assert:: ...
}

// spuštění sady testů
$testCase = new VillageFacadeTest();
$testCase->run();
