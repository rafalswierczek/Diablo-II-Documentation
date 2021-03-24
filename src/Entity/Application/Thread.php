<?php declare(strict_types=1);

namespace App\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Application\ThreadRepository")
 * @ORM\Table(name="`thread`")
 */
class Thread
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="thread.user1.empty")
     * @Assert\Type("User")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="threads")
     * @ORM\JoinColumn(name="user1_id", referencedColumnName="id")
     */
    private $user1;

    /**
     * @Assert\NotBlank(message="thread.user2.empty")
     * @Assert\Type("User")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="threads")
     * @ORM\JoinColumn(name="user2_id", referencedColumnName="id")
     */
    private $user2;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $add_date;
    

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser1(): User
    {
        return $this->user1;
    }
    public function setUser1(User $user1): self
    {
        $this->user1 = $user1;
        return $this;
    }

    public function getUser2(): User
    {
        return $this->user2;
    }
    public function setUser2(User $user2): self
    {
        $this->user2 = $user2;
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