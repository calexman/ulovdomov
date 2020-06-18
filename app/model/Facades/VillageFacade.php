<?php

/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Facades;

use App\Model\Entities\Village;
use Doctrine\ORM\EntityManager;

/**
 * Fasáda pro manipulaci s lokalitami
 * @package App\Model\Facades
 */
class VillageFacade extends BaseFacade
{
    
        /**
     * Konstruktor fasády
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->repository = $this->em->getRepository(Village::class);
    }
    
    /**
     * Získá všechny dostupné lokality
     * @return pole lokalit ve formátu [Village.id => Village];
     */
    public function getAllVillages() : Array
    {
        $data =  $this->repository->findAll();
        return array_reduce($data, function($result, $item) {
            $result[$item->id] = $item;
            return $result;
        });
    }
}
