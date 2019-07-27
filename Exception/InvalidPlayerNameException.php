<?php
namespace Likemusic\AutomatedUpdatePlayersGames;

use InvalidArgumentException;
use Throwable;

class InvalidPlayerNameException extends InvalidArgumentException
{
    /**
     * @var string
     */
    private $playerName;

    /**
     * InvalidPlayerNameException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param string $playerName
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, string $playerName = null)
    {
        if (!$message) {
            $message = 'Invalid source player name: '. $playerName;
        }

        parent::__construct($message, $code, $previous);

        $this->playerName = $playerName;
    }

    public function getPlayerName()
    {
        return $this->playerName;
    }
}
