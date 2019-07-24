<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper\Cron;

use DateTime;
use Exception;

class Manager
{
    /**
     * @param string $hookName
     * @param int $period
     * @param int $hours
     * @param int $minutes
     * @throws Exception
     */
    public function addTask($hookName, $period, $hours, $minutes)
    {
        $timestamp = $this->getTimestampByParams($hours, $minutes);

        if (!wp_next_scheduled($hookName) ) {
            wp_schedule_event($timestamp, $period, $hookName);
        }
    }

    /**
     * @param int $hours
     * @param int $minutes
     * @return int
     * @throws Exception
     */
    private function getTimestampByParams($hours, $minutes)
    {
        $dateTime = new DateTime();
        $dateTime->setTime($hours, $minutes);

        return $dateTime->getTimestamp();
    }

    /**
     * @param string $hookName
     */
    public function deleteTask($hookName)
    {
        if (wp_next_scheduled($hookName) ) {
            wp_clear_scheduled_hook($hookName);
        }
    }
}
