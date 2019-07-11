<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use Likemusic\AutomatedUpdatePlayersGames\Contracts\HooksInterface;

class Cron
{
    const HOOK_NAME = HooksInterface::UPDATE_PLAYERS_GAMES;

    public function addTask()
    {
        if (!wp_next_scheduled(self::HOOK_NAME) ) {
            wp_schedule_event(time(), 'hourly', self::HOOK_NAME);
        }
    }

    public function deleteTask()
    {
        if (wp_next_scheduled(self::HOOK_NAME) ) {
            wp_clear_scheduled_hook(self::HOOK_NAME);
        }
    }
}
