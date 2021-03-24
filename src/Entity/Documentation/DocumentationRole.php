<?php declare(strict_types=1);

namespace App\Entity\Documentation;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentationRoleRepository")
 * @ORM\Table(name="documentation_role")
 */
class DocumentationRole
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(max=50, maxMessage="documentation_role.name_pl.max_length|{{ limit }}")
     */
    private $name_pl;

    /**
     * @Assert\Length(max=50, maxMessage="documentation_role.name_en.max_length|{{ limit }}")
     */
    private $name_en;

    /**
     * @Assert\Length(max=1000, maxMessage="documentation_role.description_pl.max_length|{{ limit }}")
     */
    private $description_pl;

    /**
     * @Assert\Length(max=1000, maxMessage="documentation_role.description_en.max_length|{{ limit }}")
     */
    private $description_en;

    
    public function getId(): int
    {
        return $this->id;
    }

    public function getNamePl(): string
    {
        return $this->name_pl;
    }
    public function setNamePl(string $name_pl): self
    {
        $this->name_pl = $name_pl;
        return $this;
    }

    public function getNameEn(): string
    {
        return $this->name_en;
    }
    public function setNameEn(string $name_en): self
    {
        $this->name_en = $name_en;
        return $this;
    }

    public function getDescriptionPl(): string
    {
        return $this->description_pl;
    }
    public function setDescriptionPl(string $description_pl): self
    {
        $this->description_pl = $description_pl;
        return $this;
    }

    public function getDescriptionEn(): string
    {
        return $this->description_en;
    }
    public function setDescriptionEn(string $description_en): self
    {
        $this->description_en = $description_en;
        return $this;
    }
}