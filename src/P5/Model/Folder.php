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
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @Gedmo\Tree(type="nested")FolderRepository
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

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="folders")
     */
    private $user;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    public function setParent(Folder $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return mixed
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param mixed $documents
     */
    public function setDocuments(Document $documents)
    {
        $this->documents = $documents;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function __toString()
    {
        $prefix = "";
        for ($i=2; $i<= $this->lvl; $i++){
            $prefix .= "&nbsp;&nbsp;&nbsp;&nbsp;";
            //$prefix .= "----";
        }
        return $prefix . $this->name;
    }
}