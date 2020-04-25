<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ToolRepository")
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
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picturePath;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $displayOrder;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $informationPicturePath;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="tools")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tip", mappedBy="tool", orphanRemoval=true)
     */
    private $tips;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="tool", orphanRemoval=true)
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tutorial", mappedBy="tool", orphanRemoval=true)
     */
    private $tutorial;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NoticeParagraph", mappedBy="tool", orphanRemoval=true)
     */
    private $noticeParagraph;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Characteristic", mappedBy="tool", orphanRemoval=true)
     */
    private $characteristic;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consumable", mappedBy="tool", orphanRemoval=true)
     */
    private $consumable;

    public function __construct()
    {
        $this->tips = new ArrayCollection();
        $this->question = new ArrayCollection();
        $this->tutorial = new ArrayCollection();
        $this->noticeParagraph = new ArrayCollection();
        $this->characteristic = new ArrayCollection();
        $this->relation = new ArrayCollection();
        $this->consumable = new ArrayCollection();
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

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(?string $picturePath): self
    {
        $this->picturePath = $picturePath;

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

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): self
    {
        $this->informations = $informations;

        return $this;
    }

    public function getInformationPicturePath(): ?string
    {
        return $this->informationPicturePath;
    }

    public function setInformationPicturePath(?string $informationPicturePath): self
    {
        $this->informationPicturePath = $informationPicturePath;

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
     * @return Collection|NoticeParagraph[]
     */
    public function getNoticeParagraph(): Collection
    {
        return $this->noticeParagraph;
    }

    public function addNoticeParagraph(NoticeParagraph $noticeParagraph): self
    {
        if (!$this->noticeParagraph->contains($noticeParagraph)) {
            $this->noticeParagraph[] = $noticeParagraph;
            $noticeParagraph->setTool($this);
        }

        return $this;
    }

    public function removeNoticeParagraph(NoticeParagraph $noticeParagraph): self
    {
        if ($this->noticeParagraph->contains($noticeParagraph)) {
            $this->noticeParagraph->removeElement($noticeParagraph);
            // set the owning side to null (unless already changed)
            if ($noticeParagraph->getTool() === $this) {
                $noticeParagraph->setTool(null);
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
}
