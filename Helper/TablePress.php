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

        if ($table instanceof WP_Error) {
            /** @var WP_Error $table */
            $wpErrorMessage = $this->getExceptionMessageByWpError($table);

            throw new Exception("Error on load table with id \"{$tableId}\". WP Error: {$wpErrorMessage}");
        }

        return $table;
    }

    private function getExceptionMessageByWpError(WP_Error $error)
    {
        $errorCode = $error->get_error_code();
        $errorMessage = $error->get_error_message();
        $errorData = $error->get_error_data();

        return "code={$errorCode}, message={$errorMessage}, data={$errorData}";
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
