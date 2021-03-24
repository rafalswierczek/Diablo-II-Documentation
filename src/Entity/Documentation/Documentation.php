<?php declare(strict_types=1);

namespace App\Entity\Documentation;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentationRepository")
 * @ORM\Table(name="documentation")
 * @UniqueEntity(fields={"name"}, message="documentation.name.duplicate")
 */
class Documentation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="documentation.name.empty")
     * @Assert\Length(max=60, maxMessage="documentation.name.max_length|{{ limit }}")
     * @ORM\Column(type="string", length=60)
     */
    private $name;

    /**
     * @Assert\NotBlank(message="documentation.default_language.empty")
     * @Assert\Length(exactly=2, exactMessage = "documentation.name.exact_length|{{ limit }}")
     * @ORM\Column(type="string", length=2)
     */
    private $default_language;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $add_date;


    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;
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

    public function getDefaultLanguage(): string
    {
        return $this->default_language;
    }
    public function setDefaultLanguage(string $defaultLanguage): self
    {
        $this->default_language = $defaultLanguage;
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
}