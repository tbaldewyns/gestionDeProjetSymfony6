<?php

namespace App\Entity;

use Serializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
/**
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'adresse email correspond à un compte existant."
 * )
 */
#[ORM\Table(name: '`user`')]
#[ApiResource(
    collectionOperations: [
        'me' => [
            'pagination_enabled' => false,
            'path' => '/me',
            'method' => 'get',
            'controller' => MeController::class,
            'read' => false,
            'openapi_context' => [
                'security' => ['cookieAuth' => []]
            ]
        ],
        'post' => [
            'path' => '/users',
        ]
    ],
    itemOperations: [
        'get' => [
            'controller' => NotFoundAction::class,
            'read' => false,
            'output' => false
        ]
    ],
    normalizationContext: ['groups' => ['read:User'], ['read:Child']]


)]
class User implements UserInterface , \Serializable, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Votre prénom doit faire au moins {{ limit }} cacractères",
     *      maxMessage = "Votre prénom ne peut pas faire plus de {{ limit }} cacractères"
     * )
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;
    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Votre nom doit faire au moins {{ limit }} cacractères",
     *      maxMessage = "Votre nom ne peut pas faire plus de {{ limit }} cacractères"
     * )
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    private $accountType;
    /**
     * @Assert\Length(min="6", minMessage="Votre mot de passe doit faire minimum 6 caracteres")
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $password;
    /**
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passes ne correspondent pas !")
     */
    public $confirmPassword;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): self
    {
        $this->accountType = $accountType;

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

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getRoles():array
    {
        return array($this->accountType);
    }
    public function eraseCredentials(){

    }
    public function getUserIdentifier(): string{
        return $this->id;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }
}