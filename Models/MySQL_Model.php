<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/../Helpers/MySQL_Helper.php';
require_once __DIR__ . '/../Config.php';

class MySQL_Model 
{

    protected $database;
    
    public function __construct($database)
    {
        $this->database = $database;

        $createTable = $this->create();

        if( !$createTable )
        {
            exit("Create table error => " . $createTable->error);
        }
    }

    /**
     * Создаёт таблицу если её нет
     */
    protected function create()
    {
        return $this->connection()->query('
            CREATE TABLE IF NOT EXISTS `' . Config::PAYMENTS['table'] . '` (
                ' . \MYSQL_Helper::normalizeCreateColums(Config::PAYMENTS['colums']) . '
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    /**
     * получает данные из даблицы
     * 
     * Пример:
     *  'table', 
     *  [
     *      'column1' => 'value1',
     *      'column2' => 'value2'
     *  ], 
     *  [
     *      'column1' => 'value1',
     *      'column2' => 'value2'
     *  ]
     */
    protected function get($table, $columns, $where)
    {
        $result = $this->connection()->query('SELECT ' . \MySQL_Helper::normalizeColumns($columns) . ' FROM `' . $table . '` WHERE ' . \MySQL_Helper::normalizeWhere($where));

        return $result ? $result->fetch_object() : null;
    }

    /**
     * Обновляет данные в таблице
     * 
     * Пример:
     *  'table', 
     *  [
     *      'column1' => 'value2',
     *      'column2' => 'value2'
     *  ], 
     *  [
     *      'column1' => 'value1',
     *      'column2' => 'value2'
     *  ]
     */
    protected function update($table, $sets, $where)
    {
        return $this->connection()->query('UPDATE ' . $table . ' SET ' . \MySQL_Helper::normalizeSets($sets) . ' WHERE ' . \MySQL_Helper::normalizeWhere($where)) OR false;
    }

    /**
     * Записывает новые данные в таблицу
     * 
     * Пример:
     *  'table', 
     *  [
     *      'column1' => 'value2',
     *      'column2' => 'value2'
     *  ]
     */
    protected function insert($table, $values)
    {
        return $this->connection()->query('INSERT INTO `' . $table . '` ' . \MySQL_Helper::normalizeValues($values)) OR false;
    }

    /**
     * Пробует подключиться к базе данных
     * и возврщает её в случае успеха
     */
    protected function connection()
    {
        $mysqli = new mysqli($this->database['host'], $this->database['login'], $this->database['password'], $this->database['name'], $this->database['port']);
		if ($mysqli->connect_errno) {
			exit('MySQL ERROR!');
		}
		return $mysqli;
    }
}

?>