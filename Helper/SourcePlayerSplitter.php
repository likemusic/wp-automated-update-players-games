<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use InvalidArgumentException;

class SourcePlayerSplitter
{
    public function getLatinPlayerNameParts($sourcePlayer)
    {
        $pattern = '/(?<lastName>[\w-]+) (?<firstNameFirstLetters>\w.+) \((?<country>\w+)\)/';
        $matches = [];

        if (!preg_match($pattern, $sourcePlayer, $matches)) {
            throw new InvalidArgumentException('Invalid source player name: '. $sourcePlayer);
        }

        return [
            $matches['lastName'],
            $matches['firstNameFirstLetters'],
            $matches['country'],
        ];
    }
}
