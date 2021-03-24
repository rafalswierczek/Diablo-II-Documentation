<?php declare(strict_types=1);

namespace App\Service\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class DocumentationForm
{
    private string $docName;
    private string $defaultLanguage;
    private FileType $string;
    private FileType $expansionstring;
    private FileType $patchstring;
    private FileType $images;
    private FileType $animdata;
    private FileType $monstats;
    private FileType $properties;
    private FileType $itemstatcost;
    private FileType $skills;
    private FileType $skilldesc;
    private FileType $misc;
    private FileType $armor;
    private FileType $weapons;
    private FileType $uniqueitems;

    public function getDocName(): string
    {
        return $this->docName;
    }
    public function setDocName(string $docName): self
    {
        $this->docName = $docName;
        return $this;
    }

    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }
    public function setDefaultLanguage(string $defaultLanguage): self
    {
        $this->defaultLanguage = $defaultLanguage;
        return $this;
    }

    public function getString(): FileType
    {
        return $this->string;
    }
    public function setString(FileType $string): self
    {
        $this->string = $string;
        return $this;
    }

    public function getExpansionstring(): FileType
    {
        return $this->expansionstring;
    }
    public function setExpansionstring(FileType $expansionstring): self
    {
        $this->expansionstring = $expansionstring;
        return $this;
    }

    public function getPatchstring(): FileType
    {
        return $this->patchstring;
    }
    public function setPatchstring(FileType $patchstring): self
    {
        $this->patchstring = $patchstring;
        return $this;
    }

    public function getImages(): FileType
    {
        return $this->images;
    }
    public function setImages(FileType $images): self
    {
        $this->images = $images;
        return $this;
    }

    public function getAnimdata(): FileType
    {
        return $this->animdata;
    }
    public function setAnimdata(FileType $animdata): self
    {
        $this->animdata = $animdata;
        return $this;
    }

    public function getMonstats(): FileType
    {
        return $this->monstats;
    }
    public function setMonstats(FileType $monstats): self
    {
        $this->monstats = $monstats;
        return $this;
    }

    public function getProperties(): FileType
    {
        return $this->properties;
    }
    public function setProperties(FileType $properties): self
    {
        $this->properties = $properties;
        return $this;
    }

    public function getItemstatcost(): FileType
    {
        return $this->itemstatcost;
    }
    public function setItemstatcost(FileType $itemstatcost): self
    {
        $this->itemstatcost = $itemstatcost;
        return $this;
    }

    public function getSkills(): FileType
    {
        return $this->skills;
    }
    public function setSkills(FileType $skills): self
    {
        $this->skills = $skills;
        return $this;
    }

    public function getSkilldesc(): FileType
    {
        return $this->skilldesc;
    }
    public function setSkilldesc(FileType $skilldesc): self
    {
        $this->skilldesc = $skilldesc;
        return $this;
    }

    public function getMisc(): FileType
    {
        return $this->misc;
    }
    public function setMisc(FileType $misc): self
    {
        $this->misc = $misc;
        return $this;
    }
    
    public function getArmor(): FileType
    {
        return $this->armor;
    }
    public function setArmor(FileType $armor): self
    {
        $this->armor = $armor;
        return $this;
    }

    public function getWeapons(): FileType
    {
        return $this->weapons;
    }
    public function setWeapons(FileType $weapons): self
    {
        $this->weapons = $weapons;
        return $this;
    }
    
    public function getUniqueitems(): FileType
    {
        return $this->uniqueitems;
    }
    public function setUniqueitems(FileType $uniqueitems): self
    {
        $this->uniqueitems = $uniqueitems;
        return $this;
    }
}