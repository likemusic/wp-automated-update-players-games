<?php

namespace Likemusic\AutomatedUpdatePlayersGames;

use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Cron as CronService;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Hooks as HooksService;

class Plugin
{
    /** @var CronService */
    private $cronService;

    /**
     * @var HooksService
     */
    private $hooksService;

    /**
     * Plugin constructor.
     * @param CronService $cronService
     * @param HooksService $hooksService
     */
    public function __construct(
        CronService $cronService,
        HooksService $hooksService
    ) {
        $this->cronService = $cronService;
        $this->hooksService = $hooksService;
    }

    public function run($pluginFile)
    {
        register_activation_hook($pluginFile, [$this, 'activate']);
        register_deactivation_hook($pluginFile, [$this, 'deactivate']);

        $this->addActionsForUpdatePlayersGamesHooks();
    }

    private function addActionsForUpdatePlayersGamesHooks()
    {
        $this->hooksService->addActionsForUpdatePlayersGamesHooks();
    }

    /**
     * @throws Exception
     */
    public function activate()
    {
        $this->addCronTasks();
    }

    /**
     * @throws Exception
     */
    private function addCronTasks()
    {
        $this->cronService->addCronTasks();
    }

    public function deactivate()
    {
        $this->deleteCronTasks();
    }

    private function deleteCronTasks()
    {
        $this->cronService->deleteCronTasks();
    }
}
