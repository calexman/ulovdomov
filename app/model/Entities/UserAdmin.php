<?php
/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Doctrine entita pro tabulku user_admin.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="user_admin")
 */
class UserAdmin extends BaseEntity {
    
    /**
     * ID uživatele.
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="authorization")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
     protected $user;

    /**
     * ID lokality.
     * @ORM\Id
     * @ORM\manyToOne(targetEntity="Village")
     * @ORM\JoinColumn(name="village", referencedColumnName="id")
     */
    protected $village;

    /**
     * ID typu oprávnění.
     * @ORM\Id
     * @ORM\manyToOne(targetEntity="AuthorizationType")
     * @ORM\JoinColumn(name="authorization_type", referencedColumnName="id")
     */
    protected $authorizationType;

}
