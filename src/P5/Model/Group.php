<?php
/**
 * Created by PhpStorm.
 * User: thaiht
 * Date: 10/1/15
 * Time: 4:12 PM
 */

namespace P5\Model;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="`group`")
 */

class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    public function __construct()
    {
        parent::__construct();
    }
}