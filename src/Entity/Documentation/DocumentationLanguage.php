<?php declare(strict_types=1);

namespace App\Entity\Documentation;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentationLanguageRepository")
 * @ORM\Table(name="documentation_language")
 * @UniqueEntity(fields={"documentation", "language"}, message="documentation_language.documentation_language.duplicate")
 */
class DocumentationLanguage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("Documentation", message="documentation_language.documentation.invalid_type")
     * @ORM\ManyToOne(targetEntity="Documentation")
     * @JoinColumn(name="documentation_id", referencedColumnName="id")
     */
    private $documentation;

    /**
     * @Assert\Length(exactly=2, exactMessage = "documentation_language.language.exact_length|{{ limit }}")
     */
    private $language;

    
    public function getId(): int
    {
        return $this->id;
    }

    public function getDocumentation(): Documentation
    {
        return $this->documentation;
    }
    public function setDocumentation(Documentation $documentation): self
    {
        $this->documentation = $documentation;
        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }
}