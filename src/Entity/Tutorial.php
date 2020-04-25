<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TutorialRepository")
 */
class Tutorial
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
    private $youtubeLink;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tool", inversedBy="tutorial")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tool;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYoutubeLink(): ?string
    {
        return $this->youtubeLink;
    }

    public function setYoutubeLink(string $youtubeLink): self
    {
        $this->youtubeLink = $youtubeLink;

        return $this;
    }

    public function getTool(): ?Tool
    {
        return $this->tool;
    }

    public function setTool(?Tool $tool): self
    {
        $this->tool = $tool;

        return $this;
    }
}
