<?php declare(strict_types=1);

namespace App\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Application\RoleRepository")
 * @ORM\Table(name="`role`")
 * @UniqueEntity(fields={"name"}, message="role.name.duplicate")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=50)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $name;


    public function getCode(): string
    {
        return $this->code;
    }
    public function setCode(string $code): self
    {
        $this->code = $code;
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
}