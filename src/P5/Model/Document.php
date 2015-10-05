<?php
/**
 * Created by PhpStorm.
 * User: thaiht
 * Date: 10/1/15
 * Time: 4:12 PM
 */

namespace P5\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="`document`")
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="documents")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="documents")
     */
    private $folder;

    /**
     * @ORM\Column(name="filename", type="string", length=255, nullable = false)
     */
    private $filename;

    /**
     * @ORM\Column(name="upload_date", type="datetime")
     */
    private $uploadDate;

    /**
     * @ORM\Column(name="last_modified", type="datetime")
     */
    private $lastModified;
}