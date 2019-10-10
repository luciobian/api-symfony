<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * 
 * @ApiResource(
 * )
 */
class Image
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="url")
     */
    private $file;

    /**
     * @ORM\Column(nullable=true)
     */
    private $url;

     
    public function getId()
    {
        return $this->id;
    }

     
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
 
    public function getFile()
    {
        return $this->file;
    }

     
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

     
    public function getUrl()
    {
        return $this->url;
    }
 
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}