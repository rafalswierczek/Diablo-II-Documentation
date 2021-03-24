<?php declare(strict_types=1);

namespace App\Entity\Table;

use App\Service\Enum\AnimDataEnum;

class AnimData implements TableInterface, AnimDataEnum
{
    private string $classCode;
    private string $attackMode;
    private string $wclass;
    private int $framesPerDirection;
    private int $animationSpeed;
    private string $fileName = 'AnimData.txt';
    
    public function setClassCode(string $classCode): self
    {
        $this->classCode = $classCode;
        return $this;
    }
    public function getClassCode(): string
    {
        return $this->classCode;
    }

    public function setAttackMode(string $attackMode): self
    {
        $this->attackMode = $attackMode;
        return $this;
    }
    public function getAttackMode(): string
    {
        return $this->attackMode;
    }

    public function setWclass(string $wclass): self
    {
        $this->wclass = $wclass;
        return $this;
    }
    public function getWclass(): string
    {
        return $this->wclass;
    }

    public function setFramesPerDirection(int $framesPerDirection): self
    {
        $this->framesPerDirection = $framesPerDirection;
        return $this;
    }
    public function getFramesPerDirection(): int
    {
        return $this->framesPerDirection;
    }

    public function setAnimationSpeed(int $animationSpeed): self
    {
        $this->animationSpeed = $animationSpeed;
        return $this;
    }
    public function getAnimationSpeed(): int
    {
        return $this->animationSpeed;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}