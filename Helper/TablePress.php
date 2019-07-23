<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

use Exception;
use TablePress_Table_Model;
use WP_Error;

class TablePress
{
    /** @var TablePress_Table_Model */
    private $tablePressModel;

    public function __construct(TablePress_Table_Model $tablePressModel)
    {
        $this->tablePressModel = $tablePressModel;
    }

    /**
     * @param string $tableId
     * @return array
     * @throws Exception
     */
    public function getTableById(string $tableId): array
    {
        $table = $this->tablePressModel->load($tableId);

        if (!is_array($table)) {
            /** @var WP_Error $table */
            throw new Exception($table->get_error_message());
        }

        return $table;
    }

    public function save(array $pressTable)
    {
        return $this->tablePressModel->save($pressTable);
    }

    public function add(array $pressTable)
    {
        return $this->tablePressModel->add($pressTable);
    }

    /**
     * @param string $oldTableId
     * @param string $newTableId
     * @throws Exception
     */
    public function changeTableId($oldTableId, $newTableId)
    {
        $result = $this->tablePressModel->change_table_id($oldTableId, $newTableId);

        if($result instanceof WP_Error) {
            throw new Exception($result->get_error_message());
        }
    }
}
