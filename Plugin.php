<?php

namespace Likemusic\AutomatedUpdatePlayersGames;

use Likemusic\AutomatedUpdatePlayersGames\Helper\Cron as CronHelper;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\HooksInterface;

class Plugin
{
    /** @var CronHelper */
    private $cronHelper;

    /** @var PlayersGamesUpdater */
    private $playersGamesUpdater;

    public function __construct(CronHelper $cronHelper, PlayersGamesUpdater $playersGamesUpdater)
    {
        $this->cronHelper = $cronHelper;
        $this->playersGamesUpdater = $playersGamesUpdater;
    }

    public function run($pluginFile)
    {
        register_activation_hook($pluginFile, [$this, 'activate']);
        register_deactivation_hook($pluginFile, [$this, 'deactivate']);

        $this->addActionForUpdatePlayersGamesHook();
    }

    private function addActionForUpdatePlayersGamesHook()
    {
        add_action(HooksInterface::UPDATE_PLAYERS_GAMES, [$this->playersGamesUpdater, 'update']);
    }

    public function activate()
    {
        $this->addCronTask();
    }

    public function deactivate()
    {
        $this->deleteCronTask();
    }

    private function addCronTask()
    {
        $this->cronHelper->addTask();
    }

    private function deleteCronTask()
    {
        $this->cronHelper->deleteTask();
    }
}
