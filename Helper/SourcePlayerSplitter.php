<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

class SourcePlayerSplitter
{
    public function getLatinPlayerNameParts($sourcePlayer)
    {
        $pattern = '/(?<lastname>\w+) (?<fn>\w). \((?<country>\w+)\)/';
        $matches = [];

        if (!preg_match($pattern, $sourcePlayer, $matches)) {
            throw new \InvalidArgumentException('Invalid source player name: '. $sourcePlayerName);
        }

        return [
            $matches['lastname'],
            $matches['fn'],
            $matches['country'],
        ];
    }
}
