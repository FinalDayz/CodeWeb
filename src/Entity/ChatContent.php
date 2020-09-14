<?php

namespace App\Entity;

use App\Repository\ChatContentRepository;
use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;
use ReflectionException;

/**
 * @ORM\Entity(repositoryClass=ChatContentRepository::class)
 */
class ChatContent extends SessionContent
{
    /**
     * @ORM\Column(type="text")
     */
    private $message;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    function getType(): string
    {
        $reflect = new ReflectionClass($this);
        return $reflect->getShortName();
    }
}
