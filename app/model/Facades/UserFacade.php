<?php

/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Facades;

use App\Model\Entities\User;
use App\Model\Entities\UserAdmin;
use Doctrine\ORM\EntityManager;

/**
 * Fasáda pro manipulaci s uživateli.
 * @package App\Model\Facades
 */
class UserFacade extends BaseFacade {

    /**
     * Konstruktor fasády
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        parent::__construct($em);
        $this->repository = $this->em->getRepository(User::class);
    }

    /**
     * Najde a vrátí uživatele podle jeho ID.
     * @param int|NULL $id ID uživatele
     * @return UserEntity|NULL vrátí entitu uživatele nebo NULL pokud uživatel s ID neexistuje
     */
    public function getUser($id) {
        return isset($id) ? $this->repository->find($id) : NULL;
    }

    /**
     * Provede zápis oprávnění uživatele do databáze
     * @param User $user Entita uživateli, kterému se bude oprávnění nastavovat
     * @param array $authArray seznam nových přístupových orpávnění uživatele
     * @throws \App\Model\Facades\Exception
     */
    public function setAuthorizations(User $user, Array $authArray) {
        $this->em->getConnection()->beginTransaction();
        try {
            // smazání původních přístupových oprávnění
            $qb = $this->em->createQueryBuilder();
            $qb->delete()
                    ->from(UserAdmin::class, "ua")
                    ->where("ua.user = :userId")
                    ->setParameter("userId", $user->id)
                    ->getQuery()
                    ->execute();

            // vytvoření nových přístupových oprávnění
            foreach ($authArray as $item) {
                $entity = new UserAdmin();
                $entity->user = $user;
                $entity->village = $item["village"];
                $entity->authorizationType = $item["authType"];
                $this->em->persist($entity);
            }
            $this->em->flush();
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
        $this->em->getConnection()->commit();
    }

}
