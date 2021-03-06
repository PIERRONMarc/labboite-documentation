<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Constraints as UserAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ToolRepository")
 * @UserAssert\ToolIsUnique
 */
class Tool
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pictureName;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $displayOrder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="tools")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tip", mappedBy="tool", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $tips;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="tool", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tutorial", mappedBy="tool", orphanRemoval=true)
     */
    private $tutorial;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Characteristic", mappedBy="tool", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $characteristic;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consumable", mappedBy="tool", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $consumable;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Information", mappedBy="tool", cascade={"persist", "remove"})
     */
    private $information;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Resource", mappedBy="tool", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $resources;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Notice", inversedBy="tool", cascade={"persist", "remove"})
     */
    private $notice;

    public function __construct()
    {
        $this->tips = new ArrayCollection();
        $this->question = new ArrayCollection();
        $this->tutorial = new ArrayCollection();
        $this->characteristic = new ArrayCollection();
        $this->relation = new ArrayCollection();
        $this->consumable = new ArrayCollection();
        $this->resources = new ArrayCollection();
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

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function setPictureName(?string $pictureName): self
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Tip[]
     */
    public function getTips(): Collection
    {
        return $this->tips;
    }

    public function addTip(Tip $tip): self
    {
        if (!$this->tips->contains($tip)) {
            $this->tips[] = $tip;
            $tip->setTool($this);
        }

        return $this;
    }

    public function removeTip(Tip $tip): self
    {
        if ($this->tips->contains($tip)) {
            $this->tips->removeElement($tip);
            // set the owning side to null (unless already changed)
            if ($tip->getTool() === $this) {
                $tip->setTool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestion(): Collection
    {
        return $this->question;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->question->contains($question)) {
            $this->question[] = $question;
            $question->setTool($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->question->contains($question)) {
            $this->question->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getTool() === $this) {
                $question->setTool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tutorial[]
     */
    public function getTutorial(): Collection
    {
        return $this->tutorial;
    }

    public function addTutorial(Tutorial $tutorial): self
    {
        if (!$this->tutorial->contains($tutorial)) {
            $this->tutorial[] = $tutorial;
            $tutorial->setTool($this);
        }

        return $this;
    }

    public function removeTutorial(Tutorial $tutorial): self
    {
        if ($this->tutorial->contains($tutorial)) {
            $this->tutorial->removeElement($tutorial);
            // set the owning side to null (unless already changed)
            if ($tutorial->getTool() === $this) {
                $tutorial->setTool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Characteristic[]
     */
    public function getCharacteristic(): Collection
    {
        return $this->characteristic;
    }

    public function addCharacteristic(Characteristic $characteristic): self
    {
        if (!$this->characteristic->contains($characteristic)) {
            $this->characteristic[] = $characteristic;
            $characteristic->setTool($this);
        }

        return $this;
    }

    public function removeCharacteristic(Characteristic $characteristic): self
    {
        if ($this->characteristic->contains($characteristic)) {
            $this->characteristic->removeElement($characteristic);
            // set the owning side to null (unless already changed)
            if ($characteristic->getTool() === $this) {
                $characteristic->setTool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Consumable[]
     */
    public function getConsumable(): Collection
    {
        return $this->consumable;
    }

    public function addConsumable(Consumable $consumable): self
    {
        if (!$this->consumable->contains($consumable)) {
            $this->consumable[] = $consumable;
            $consumable->setTool($this);
        }

        return $this;
    }

    public function removeConsumable(Consumable $consumable): self
    {
        if ($this->consumable->contains($consumable)) {
            $this->consumable->removeElement($consumable);
            // set the owning side to null (unless already changed)
            if ($consumable->getTool() === $this) {
                $consumable->setTool(null);
            }
        }

        return $this;
    }

    public function getInformation(): ?Information
    {
        return $this->information;
    }

    public function setInformation(Information $information): self
    {
        $this->information = $information;

        // set the owning side of the relation if necessary
        if ($information->getTool() !== $this) {
            $information->setTool($this);
        }

        return $this;
    }
    
    public function __toString(){
        // to show the name of the Tool in the select
        return $this->name;
        // to show the id of the Tategory in the select
        // return $this->id;
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

    /**
     * @return Collection|Resource[]
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): self
    {
        if (!$this->resources->contains($resource)) {
            $this->resources[] = $resource;
            $resource->setTool($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): self
    {
        if ($this->resources->contains($resource)) {
            $this->resources->removeElement($resource);
            // set the owning side to null (unless already changed)
            if ($resource->getTool() === $this) {
                $resource->setTool(null);
            }
        }

        return $this;
    }

    public function getNotice(): ?Notice
    {
        return $this->notice;
    }

    public function setNotice(?Notice $notice): self
    {
        $this->notice = $notice;

        return $this;
    }
}
