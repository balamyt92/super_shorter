<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkRepository")
 */
class Link
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
    private $source;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $short;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    private $create_at;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $expire_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getShort(): ?string
    {
        return $this->short;
    }

    public function setShort(string $short): self
    {
        $this->short = $short;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeImmutable $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeImmutable
    {
        return $this->expire_at;
    }

    public function setExpireAt(?\DateTimeImmutable $expire_at): self
    {
        $this->expire_at = $expire_at;

        return $this;
    }
}
