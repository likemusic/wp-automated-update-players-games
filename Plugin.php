<?php

namespace Likemusic\AutomatedUpdatePlayersGames;

use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Cron as CronService;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Hooks as HooksService;
use Likemusic\AutomatedUpdatePlayersGames\Controller\AdminPage as AdminPageController;

class Plugin
{
    /** @var CronService */
    private $cronService;

    /**
     * @var HooksService
     */
    private $hooksService;

    /**
     * @var AdminPageController
     */
    private $adminPageController;

    /**
     * Plugin constructor.
     * @param CronService $cronService
     * @param HooksService $hooksService
     * @param AdminPageController $adminPageController
     */
    public function __construct(
        CronService $cronService,
        HooksService $hooksService,
        AdminPageController $adminPageController
    ) {
        $this->cronService = $cronService;
        $this->hooksService = $hooksService;
        $this->adminPageController = $adminPageController;
    }

    public function run($pluginFile)
    {
        register_activation_hook($pluginFile, [$this, 'activate']);
        register_deactivation_hook($pluginFile, [$this, 'deactivate']);

        $this->addActionsForUpdatePlayersGamesHooks();
        $this->addAdminMenuAction();
    }

    private function addAdminMenuAction()
    {
        add_action('admin_menu', [$this, 'addAdminPage']);
    }

    public function addAdminPage()
    {
        //add_submenu_page('tools.php', 'Automated update player\'s games', 'Menu title', 'manage_options', '');
        add_submenu_page(
            'tools.php',
            'Обновление истории игр игроков',
            'Обновление истории игр игроков',
            'manage_options',
            'automated-update-players-games',
            [$this->adminPageController, 'execute']
        );
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
