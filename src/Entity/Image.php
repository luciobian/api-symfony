<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Controller\UploadImageAction;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Vich\Uploadable()
 * @ApiResource(
 *     attributes={"order"={"id":"ASC"}},
 *     collectionOperations={
 *         "get",
 *         "post"={
 *             "method"="POST",
 *             "path"="/images",
 *             "controller"=UploadImageAction::class,
 *             "defaults"={"_api_receive"=false}
 *         }
 *     }
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
     * @Assert\NotNull()
     */
    private $file;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"get-blog-post-with-author"})
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

     
    public function setFile($file):void
    {
        $this->file = $file;

    }

     
    public function getUrl()
    {
        return '/images/'.$this->url;
    }
 
    public function setUrl($url):void
    {
        $this->url = $url;

    }
}