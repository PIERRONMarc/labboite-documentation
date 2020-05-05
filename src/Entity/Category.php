<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")j
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $displayOrder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Theme", inversedBy="categories", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $thumbnailName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tool", mappedBy="category", cascade={"remove"})
     */
    private $tools;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function __construct()
    {
        $this->tools = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getThumbnailName(): ?string
    {
        return $this->thumbnailName;
    }

    public function setThumbnailName(?string $thumbnailName): self
    {
        $this->thumbnailName = $thumbnailName;

        return $this;
    }

    /**
     * @return Collection|Tool[]
     */
    public function getTools(): Collection
    {
        return $this->tools;
    }

    public function addTool(Tool $tool): self
    {
        if (!$this->tools->contains($tool)) {
            $this->tools[] = $tool;
            $tool->setCategory($this);
        }

        return $this;
    }

    public function removeTool(Tool $tool): self
    {
        if ($this->tools->contains($tool)) {
            $this->tools->removeElement($tool);
            // set the owning side to null (unless already changed)
            if ($tool->getCategory() === $this) {
                $tool->setCategory(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
