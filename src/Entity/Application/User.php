<?php declare(strict_types=1);

namespace App\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Application\UserRepository")
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"login"}, message="user.login.duplicate")
 * @UniqueEntity(fields={"email"}, message="user.email.duplicate")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="user.login.empty")
     * @Assert\Length(max=50, maxMessage="user.login.max_length|{{ limit }}")
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $login;

    /**
     * @Assert\NotBlank(message="user.password.empty")
     * @Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*()_+,.\\\/;':"-]).{8,35}$/", message="user.password.invalid")
     * @ORM\Column(type="string", length=60)
     */
    private $password;

    /**
     * @Assert\NotBlank(message="user.name.empty")
     * @Assert\Length(max=35, maxMessage="user.name.max_length{{ limit }}")
     * @ORM\Column(type="string", length=35)
     */
    private $name;
    
    /**
     * @Assert\NotBlank(message = "user.email.empty")
     * @Assert\Length(max=80, maxMessage="user.email.max_length{{ limit }}")
     * @Assert\Email(message = "user.email.invalid")
     * @ORM\Column(type="string", length=80, unique=true)
     */
    private $email;

    /**
     * @Assert\Length(max=2000, maxMessage="user.description.max_length{{ limit }}")
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $description;

    /**
     * @Assert\Choice({"amazon", "assassin", "necromancer", "barbarian", "paladin", "sorcerer", "druid", ""}, message="user.character.invalid")
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $character;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $add_date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_code", referencedColumnName="code")})
     */
    private $roles;


    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }
    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCharacter(): ?string
    {
        return $this->character;
    }
    public function setCharacter(?string $character): self
    {
        $this->character = $character;
        return $this;
    }

    public function getAddDate(): \DateTimeImmutable
    {
        return $this->add_date;
    }
    public function setAddDate(\DateTimeImmutable $addDate): self
    {
        $this->add_date = $addDate;
        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }
    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function addRole(Role $role)
    {
        $this->roles->add($role);
    }

    public function getRoles(bool $returnObjectList = false): array
    {
        if($this->roles->isEmpty()) // always add basic role in case it doesn't exists
        {
            $roleObj = new Role();
            $roleObj->setName("Basic user");
            $roleObj->setCode("ROLE_USER");
            $this->roles->add($roleObj);
        }
        
        $roleObjects = $this->roles->toArray();

        foreach($roleObjects as $role)
            $codes[] = $role->getCode();

        return $returnObjectList ? $roleObjects : $codes;
    }

    public function getUsername(): string{ return $this->login;}
    public function getSalt(){}
    public function eraseCredentials(){}
}
