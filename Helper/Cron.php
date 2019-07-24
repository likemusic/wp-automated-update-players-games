<?php
namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\WordpressPeriodInterface;
use Likemusic\AutomatedUpdatePlayersGames\Contracts\HooksInterface;
use Likemusic\AutomatedUpdatePlayersGames\Helper\Cron\Manager as CronManager;

class Cron
{
    // Hook Name, Period, Start Hours, Start Minutes
    private $cronTasksMap = [
        [HooksInterface::UPDATE_CURRENT_PLAYERS_GAMES, WordpressPeriodInterface::HOURLY, 12, 0],
        [HooksInterface::UPDATE_YESTERDAY_PLAYERS_GAMES, WordpressPeriodInterface::DAILY, 12, 0],
    ];

    /**
     * @var CronManager
     */
    private $cronManager;

    /**
     * Cron constructor.
     * @param CronManager $cronManager
     */
    public function __construct(CronManager $cronManager)
    {
        $this->cronManager = $cronManager;
    }

    /**
     * @throws Exception
     */
    public function addCronTasks()
    {
        foreach ($this->cronTasksMap as $cronTaskParams) {
            list($hookName, $period, $hours, $minutes) = $cronTaskParams;
            $this->addCronTask($hookName, $period, $hours, $minutes);
        }
    }

    /**
     * @param string $hookName
     * @param int $period
     * @param int $hours
     * @param int $minutes
     * @throws Exception
     */
    private function addCronTask($hookName, $period, $hours, $minutes)
    {
        $this->cronManager->addTask($hookName, $period, $hours, $minutes);
    }

    public function deleteCronTasks()
    {
        foreach ($this->cronTasksMap as $cronTaskParams) {
            list($hookName) = $cronTaskParams;
            $this->cronManager->deleteTask($hookName);
        }
    }
}
