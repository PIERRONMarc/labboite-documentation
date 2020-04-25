<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tool", inversedBy="question")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tool;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

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
