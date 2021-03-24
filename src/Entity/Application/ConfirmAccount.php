<?php declare(strict_types=1);

namespace App\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Application\ConfirmAccountRepository")
 * @ORM\Table(name="confirm_account")
 * @UniqueEntity(fields={"user"}, repositoryMethod="findUsers", message="confirm_account.user.duplicate")
 */
class ConfirmAccount
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=100)
     */
    private $hash;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $add_date;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)
     */
    private $user;


    public function getHash(): string
    {
        return $this->hash;
    }
    public function setHash(string $hash): self
    {
        $this->hash = $hash;
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

    public function getUser(): User
    {
        return $this->user;
    }
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}