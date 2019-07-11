<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use TablePress_Table_Model;

class TablePress
{
    /** @var TablePress_Table_Model */
    private $tablePressModel;

    public function __construct(TablePress_Table_Model $tablePressModel)
    {
        $this->tablePressModel = $tablePressModel;
    }

    public function getTableById(string $tableId)
    {
        return $this->tablePressModel->load($tableId);
    }

    public function save(array $pressTable)
    {
        return $this->tablePressModel->save($pressTable);
    }
}
