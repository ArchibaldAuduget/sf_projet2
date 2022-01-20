<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $test1;

    #[ORM\Column(type: 'integer')]
    private $test2;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTest1(): ?string
    {
        return $this->test1;
    }

    public function setTest1(string $test1): self
    {
        $this->test1 = $test1;

        return $this;
    }

    public function getTest2(): ?int
    {
        return $this->test2;
    }

    public function setTest2(int $test2): self
    {
        $this->test2 = $test2;

        return $this;
    }
}
