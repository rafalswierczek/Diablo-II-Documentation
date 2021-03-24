<?php declare(strict_types=1);

namespace App\Entity\Documentation;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \App\Entity\Application\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentationContributorRepository")
 * @ORM\Table(name="documentation_contributor")
 * @UniqueEntity(fields={"documentation", "user", "role"}, message="documentation_contributor.documentation_user_role.duplicate")
 */
class DocumentationContributor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("Documentation", message="documentation_contributor.documentation.invalid_type")
     * @ORM\ManyToOne(targetEntity="Documentation")
     * @JoinColumn(name="documentation_id", referencedColumnName="id")
     */
    private $documentation;

    /**
     * @Assert\Type("User", message="documentation_contributor.user.invalid_type")
     * @ORM\ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @Assert\Type("DocumentationRole", message="documentation_contributor.documentation_role.invalid_type")
     * @ORM\ManyToOne(targetEntity="DocumentationRole")
     * @JoinColumn(name="documentation_role_id", referencedColumnName="id")
     */
    private $documentation_role;


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

    public function getUser(): User
    {
        return $this->user;
    }
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDocumentationRole(): DocumentationRole
    {
        return $this->documentation_role;
    }
    public function setDocumentationRole(DocumentationRole $documentation_role): self
    {
        $this->documentation_role = $documentation_role;
        return $this;
    }
}