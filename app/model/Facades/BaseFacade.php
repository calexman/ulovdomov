<?php

/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Facades;

use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\Repository;
use Nette;

/**
 * Rodičovská fasáda pro všechny použité fasády
 * @package App\Model\Facades
 */
class BaseFacade
{
    use Nette\SmartObject;
    
    /** @var EntityManager Manager pro práci s entitami. */
    protected $em;
    
    /** @var Repository Reference na repozitář fasády */
    protected $repository;

    /**
     * Konstruktor fasády
     * @param EntityManager $em DI entity manageru
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
}
