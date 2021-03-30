<?php declare(strict_types=1);

namespace App\Entity\Documentation;

class AttackSpeed
{
    private string $weaponCode;
    private string $attackMode;
    private string $wclass;
    private float $attackSpeed;
    
    public function setWeaponCode(string $weaponCode): self
    {
        $this->weaponCode = $weaponCode;
        return $this;
    }
    public function getWeaponCode(): string
    {
        return $this->weaponCode;
    }

    public function setCharacter(string $character): self
    {
        $this->character = $character;
        return $this;
    }
    public function getCharacter(): string
    {
        return $this->character;
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

    public function setAttackSpeed(float $attackSpeed): self
    {
        $this->attackSpeed = $attackSpeed;
        return $this;
    }
    public function getAttackSpeed(): float
    {
        return $this->attackSpeed;
    }
}