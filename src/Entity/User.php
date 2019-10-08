<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *  @ApiResource(
 *      itemOperations={
 *          "get"={
 *               "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *               "normalization_context"={
 *                  "groups"="get"
 *               }
 *          },
 *          "put"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "denormalization_context"={
 *                 "groups"={"put"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *               }
 *          }
 *          
 *      },
 *      collectionOperations={
 *          "post"={
 *              "denormalization_context"={
 *                 "groups"={"post"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *               }
 *          }
 *      }      
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository"),
 * @UniqueEntity("username"),
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    const ROLE_COMMENTATOR = "ROLE_COMMENTATOR";
    const ROLE_WRITER = "ROLE_WRITER";
    const ROLE_EDITOR = "ROLE_EDITOR";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_SUPERADMIN = "ROLE_SUPERADMIN";
    const DEFAULT_ROLE = [self::ROLE_COMMENTATOR];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get","post","get-comment-with-author"})
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     * @Assert\Regex(
     *      pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *      message="Password must be seven characters long and contain at least on digit, one uppercase letter and one lowercase letter."
     * )
     * @Groups({"post", "put"})
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Expression(
     *      "this.getPassword() === this.getRetypedPassword()",
     *      message="Password does not match."
     * )
     * @Groups({"post", "put"})
     */
    private $retypedPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get"})
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     * @Groups({"post", "put", "get-comment-with-author"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(min=6, max=255)
     * @Groups({"post", "put", "get-admin", "get-owner"})
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author")
     * @Groups({"get"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     * @Groups({"get"})
     */
    private $comments;
    
    /**
     * @ORM\Column(type="simple_array", length=200)
     * @Groups({"get-admin", "get-owner"})
     */
    private $roles;    

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();  
        $this->roles = self::DEFAULT_ROLE;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getComments() : Collection
    {
        return $this->comments;
    }
     
    public function getPosts() : Collection
    {
        return $this->posts;
    }

    public function getRoles() : array
    {
        return $this->roles;
    }

    public function setRoles(array $roles) 
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        
    }

    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($retypedPassword) : self
    {
        $this->retypedPassword = $retypedPassword;

        return $this;
    }
}
