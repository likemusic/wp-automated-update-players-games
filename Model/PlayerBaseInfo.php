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

    public function getPostName()
    {
        return $this->postName;
    }

    public function setPostName($postName)
    {
        $this->postName = $postName;

        return $this;
    }

    public function getPostTitle()
    {
        return $this->postTitle;
    }

    public function setPostTitle($postTitle): self
    {
        $this->postTitle = $postTitle;

        return $this;
    }

    public function getTableShortCode()
    {
        return $this->shortCode;
    }

    public function setTableShortCode($shortCode): self
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    public function getLatinCountryCode()
    {
        return $this->countryCode;
    }

    public function setLatinCountryCode($countryCode): self
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
