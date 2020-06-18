<?php
/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
use Doctrine\Common\Collections\Criteria;
use App\Model\Entities\AuthorizationType;

/**
 * Doctrine entita pro tabulku user.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseEntity {

    /**
     * ID uživatele.
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Jméno uživatele.
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * Seznam autorizací, které patří uživateli
     * @ORM\OneToMany(targetEntity="UserAdmin", mappedBy="user")
     */
    protected $authorization;

    /**
     * Získá pole všech autorizací pro daný typ autorizace
     * @param AuthorizationType $authType Entita požadovaného typu autorizace
     * @return array pole entit autorizací odpovídajících typu autorizace
     */
    public function getAuthorizationByType(AuthorizationType $authType): Array {
        $criteria = (Criteria::create())->where(Criteria::expr()->eq("authorizationType", $authType));
        return $this->authorization->matching($criteria)->toArray();
    }

}
