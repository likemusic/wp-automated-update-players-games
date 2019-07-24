<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use Likemusic\AutomatedUpdatePlayersGames\PlayersGamesUpdater;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\HooksInterface;

class Hooks
{
    /** @var PlayersGamesUpdater */
    private $playersGamesUpdater;

    /**
     * Hooks constructor.
     * @param PlayersGamesUpdater $playersGamesUpdater
     */
    public function __construct(PlayersGamesUpdater $playersGamesUpdater)
    {
        $this->playersGamesUpdater = $playersGamesUpdater;
    }

    public function addActionsForUpdatePlayersGamesHooks()
    {
        add_action(HooksInterface::UPDATE_CURRENT_PLAYERS_GAMES, [$this->playersGamesUpdater, 'updateCurrent']);
        add_action(HooksInterface::UPDATE_YESTERDAY_PLAYERS_GAMES, [$this->playersGamesUpdater, 'updateYesterday']);
    }
}
