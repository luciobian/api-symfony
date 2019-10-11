<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Controller\ResetPasswordAction;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 *  @ApiResource(
 *      itemOperations={
 *          "get"={
 *               "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *               "normalization_context"={
 *                  "groups"={"get"}
 *               }
 *          },
 *          "put"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "denormalization_context"={
 *                 "groups"={"put"},
 *               },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          },
 *          "put-reset-password"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user", 
 *              "method"="PUT",
 *              "path"="/users/{id}/reset-password",
 *              "requirements"={"id"="\d+"},
 *              "controller"=ResetPasswordAction::class,
 *              "denormalization_context"={
 *                 "groups"={"put-reset-password"}
 *              },
 *              "validation_groups"={"put-reset-password"}
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
 *               },
 *              "validation_groups"={"post"}
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
     * @Groups({"post"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Length(min=6, max=255, groups={"post"})
     * @Assert\Regex(
     *      pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *      message="Password must be seven characters long and contain at least on digit, one uppercase letter and one lowercase letter.",
     *      groups={"post"}
     * )
     */
    private $password;

    /**
     * @Groups({"post"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Expression(
     *      "this.getPassword() === this.getRetypedPassword()",
     *      message="Password does not match.",
     *      groups={"post"}
     * )
     */
    private $retypedPassword;
    
    /**    
    * @Groups({"put-reset-password"})
    * @Assert\NotBlank(groups={"put-reset-password"})
    * @Assert\Length(min=6, max=255,groups={"put-reset-password"})
    * @Assert\Regex(
    *      pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
    *      message="Password must be seven characters long and contain at least on digit, one uppercase letter and one lowercase letter.",
    *      groups={"put-reset-password"}
    * )
    */
    private $newPassword;
    
    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Assert\Expression(
     *      "this.getNewPassword() === this.getNewRetypedPassword()",
     *      message="Password does not match.",
     *      groups={"put-reset-password"}
     * )
     */
    private $newRetypedPassword;
    
    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @UserPassword(groups={"put-reset-password"})
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Length(min=6, max=255, groups={"post", "put"})
     * @Groups({"post", "put", "get-comment-with-author"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Email(groups={"post", "put"})
     * @Assert\Length(min=6, max=255,groups={"post","put"})
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
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string",length=40,nullable=true)
     */
    private $confirmationToken;

    
    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();  
        $this->roles = self::DEFAULT_ROLE;
        $this->enabled = false;
        $this->confirmationToken = null;
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

    public function getComments() 
    {
        return $this->comments;
    }
     
    public function getPosts() 
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

    public function getNewPassword() :?string
    {
        return $this->newPassword;
    }

    
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

     
    public function getNewRetypedPassword() :?string
    {
        return $this->newRetypedPassword;
    }

    
    public function setNewRetypedPassword($newRetypedPassword)
    {
        $this->newRetypedPassword = $newRetypedPassword;

        return $this;
    }
    public function getOldPassword():?string
    {
        return $this->oldPassword;
    }


    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }
 
    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    } 

    public function setPasswordChangeDate($passwordChangeDate)
    {
        $this->passwordChangeDate = $passwordChangeDate;

        return $this;
    }

     
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

     
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
