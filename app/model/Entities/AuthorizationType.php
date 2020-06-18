<?php

/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Doctrine entita pro tabulku authorization_type.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="authorization_type")
 */
class AuthorizationType extends BaseEntity{
    /**
     * ID oprávnění.
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * kód oprávnění
     * @ORM\Column(type="string")
     */
    protected $code;
}