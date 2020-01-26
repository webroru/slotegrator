<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Winning
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups("api")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Award")
     * @Groups("api")
     */
    private $award;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Groups("api")
     */
    private $amount;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("api")
     */
    private $isFinished;

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getAward(): Award
    {
        return $this->award;
    }

    public function setAward(Award $award): self
    {
        $this->award = $award;
        return $this;
    }

    public function getIsFinished(): bool
    {
        return $this->isFinished;
    }

    public function setFinished(): self
    {
        $this->isFinished = true;
        return $this;
    }

    public function setUnFinished(): self
    {
        $this->isFinished = false;
        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
}
