<?php
/**
 * @author Jiří Němeček (calexman.jn@gmail.com)
 * @date 17.6.2020
 */

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Doctrine entita pro tabulku village.
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="village")
 */
class Village extends BaseEntity{
    /**
     * ID lokality
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Jméno lokality
     * @ORM\Column(type="string")
     */
    protected $name;
}