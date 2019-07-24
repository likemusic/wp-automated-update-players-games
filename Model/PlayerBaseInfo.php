<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Model;

class PlayerBaseInfo
{
    /** @var int */
    private $postId;

    /** @var string */
    private $postName;

    /** @var string */
    private $postTitle;

    /** @var string */
    private $shortCode;

    private $countryCode;

    private $latinName;

    public function getPostId()
    {
        return $this->postId;
    }

    public function setPostId($postId): self
    {
        $this->postId = $postId;

        return $this;
    }

    public function getPostName(): ?string
    {
        return $this->postName;
    }

    public function setPostName(?string $postName)
    {
        $this->postName = $postName;

        return $this;
    }

    public function getPostTitle(): ?string
    {
        return $this->postTitle;
    }

    public function setPostTitle(?string $postTitle): self
    {
        $this->postTitle = $postTitle;

        return $this;
    }

    public function getTableShortCode(): ?string
    {
        return $this->shortCode;
    }

    public function setTableShortCode(?string $shortCode): self
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    public function getLatinCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setLatinCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getLatinName()
    {
        return $this->latinName;
    }

    public function setLatinName($latinName)
    {
        $this->latinName = $latinName;
    }
}
