<?php declare(strict_types=1);

namespace App\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Application\NewsRepository")
 * @ORM\Table(name="news")
 */
class News
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="news.text.empty")
     * // 16383 is the max length of characters for TEXT type in UTF-8 4 byte encoding (floor((2^16-1)/4)
     * @Assert\Length(max=16383, maxMessage="news.text.max_length|{{ limit }}")
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @Assert\NotBlank(message="news.lang.empty")
     * @Assert\Locale(message="news.lang.invalid")
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $lang;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $add_date;


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

    public function getLang(): string
    {
        return $this->lang;
    }
    public function setLang(string $lang): self
    {
        $this->lang = $lang;
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