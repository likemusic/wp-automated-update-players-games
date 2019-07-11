<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;


class PlayerTableGamesUpdater
{
    public function updateGamesIfNecessary($playerName, $tableRowData)
    {
        $gamesTable = $this->getPlayerGamesTableByPlayerName($playerName);
        $this->updateTableIfNecessary($gamesTable, $tableRowData);
    }

    private function updateTableIfNecessary($gamesTable, $tableRowData)
    {
        $existsGame = $this->getTableGame($gamesTable, $tableRowData);

        if (!$existsGame) {
            $this->addGameToTable($gamesTable, $tableRowData);
            return;
        }

        if ($existsGame == $tableRowData) {
            return;
        }

        $this->updateTableGame($gamesTable, $tableRowData);
    }
}
