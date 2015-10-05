<?php
/**
 * Created by PhpStorm.
 * User: thaiht
 * Date: 10/2/15
 * Time: 11:40 AM
 */

namespace P5\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="P5\Repository\FolderRepository")
 * @ORM\Table(name="folder")
 */
class Folder
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="folder")
     */
    private $documents;

    public function setName($value) {
        $this->name = $value;
    }

    public function getName() {
        return $this->name;
    }
}