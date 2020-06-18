<?php

/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Facades;

use App\Model\Entities\AuthorizationType;
use Doctrine\ORM\EntityManager;

/**
 * Fasáda pro manipulaci s typy oprávnění
 * @package App\Model\Facades
 */
class AuthorizationTypeFacade extends BaseFacade {

    /**
     * Konstruktor fasády
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->repository = $this->em->getRepository(AuthorizationType::class);
    }

    /**
     * Získá entitu typu oprávnění na základě jejího textového kódu
     * @param string $code textový kód autorizace
     * @return AuthorizationType|NULL Entita typu  | NULL, pokud neexistuje
     */
    public function getAuthorizationType(string $code) : AuthorizationType {
        return isset($code) ? $this->repository->findOneBy(["code" => $code]) : NULL;
    }

    /**
     * Získá pole všech typů oprávnění
     * @return Array pole všech typů oprávnění ve formát [AuthorizationType.id=>AuthorizationType]
     */
    public function getAllAuthorizationTypes() : Array {
        $data = $this->repository->findAll();
        return array_reduce($data, function($result, $item) {
            $result[$item->id] = $item;
            return $result;
        });
    }

}
