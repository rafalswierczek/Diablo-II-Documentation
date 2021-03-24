<?php declare(strict_types=1);

namespace App\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Application\MessageRepository")
 * @ORM\Table(name="message")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="message.text.empty")
     * // 16383 is the max length of characters for TEXT type in UTF-8 4 byte encoding (floor((2^16-1)/4)
     * @Assert\Length(max=16383, maxMessage="message.text.max_length|{{ limit }}")
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $add_date;

    /**
     * @Assert\Type("User")
     * @ORM\OneToOne(targetEntity="User")
     * @JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;


    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }
    public function setText(string $text): self
    {
        $this->text = $text;
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

    public function getSender(): User
    {
        return $this->sender;
    }
    public function setSender(User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }
}