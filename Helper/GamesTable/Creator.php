<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper\GamesTable;

use Exception;
use Likemusic\AutomatedUpdatePlayersGames\Helper\TablePress as TablePressHelper;
use Likemusic\AutomatedUpdatePlayersGames\Model\PlayerBaseInfo;

class Creator
{
    /**
     * @var TablePressHelper
     */
    private $tablePressHelper;

    /**
     * Creator constructor.
     * @param TablePressHelper $tablePressHelper
     */
    public function __construct(TablePressHelper $tablePressHelper)
    {
        $this->tablePressHelper = $tablePressHelper;
    }

    /**
     * @param PlayerBaseInfo $playerBaseInfo
     * @param array $playerTableRowData
     * @return string
     * @throws Exception
     */
    public function createTableByBaseInfo(PlayerBaseInfo $playerBaseInfo, array $playerTableRowData)
    {
        $tableId = $this->getTableIdByBaseInfo($playerBaseInfo);
        $tableName = $this->getTableNameByBaseInfo($playerBaseInfo);
        $tableDescription = $this->getTableDescriptionByBaseInfo($playerBaseInfo);

        $this->createAndSaveTable($tableId, $tableName, $tableDescription, $playerTableRowData);

        return $tableId;
    }

    private function getTableIdByBaseInfo(PlayerBaseInfo $playerBaseInfo)
    {
        return strtolower($playerBaseInfo->getPostName());
    }

    private function getTableNameByBaseInfo(PlayerBaseInfo $playerBaseInfo)
    {
        return $playerBaseInfo->getLatinName();
    }

    private function getTableDescriptionByBaseInfo(PlayerBaseInfo $playerBaseInfo)
    {
        return $playerBaseInfo->getPostTitle();
    }

    /**
     * @param string $tableId
     * @param string $tableName
     * @param string $tableDescription
     * @param array $playerTableRowData
     * @throws Exception
     */
    private function createAndSaveTable(string $tableId, string $tableName, string $tableDescription, array $playerTableRowData)
    {
        $table = $this->createTable($tableName, $tableDescription, $playerTableRowData);
        // невозможно сразу создать таблицу с нужным id :(
        $newTableId = $this->addTable($table);
        $this->changeTableId($newTableId, $tableId);
    }

    private function createTable(string $tableName, string $tableDescription, array $playerTableRowData)
    {
        return [
            'id' => false,
            'name' => $tableName,
            'description' => $tableDescription,
            'data' => [$playerTableRowData],
//            'last_modified' => '2019-07-23 19:52:29',
//            'author' => 4,
            'options' =>
                array (
//                    'last_editor' => 4,
                    'table_head' => true,
                    'table_foot' => false,
                    'alternating_row_colors' => true,
                    'row_hover' => true,
                    'print_name' => false,
                    'print_name_position' => 'above',
                    'print_description' => false,
                    'print_description_position' => 'below',
                    'extra_css_classes' => '',
                    'use_datatables' => true,
                    'datatables_sort' => true,
                    'datatables_filter' => true,
                    'datatables_paginate' => true,
                    'datatables_lengthchange' => true,
                    'datatables_paginate_entries' => 10,
                    'datatables_info' => true,
                    'datatables_scrollx' => false,
                    'datatables_custom_commands' => '',
                ),
            'visibility' =>
                array (
                    'rows' =>
                        array (
                            0 => 1,
                        ),
                    'columns' =>
                        array (
                            0 => 1,
                            1 => 1,
                            2 => 1,
                            3 => 1,
                            4 => 1,
                        ),
                ),
            'new_id' => false,
        ];
    }

    /**
     * @param array $table
     * @return int
     */
    private function addTable($table): int
    {
        return $this->tablePressHelper->add($table);
    }

    /**
     * @param string $oldTableId
     * @param string $newTableId
     * @throws Exception
     */
    private function changeTableId(string $oldTableId, string $newTableId)
    {
        $this->tablePressHelper->changeTableId($oldTableId, $newTableId);
    }
}
