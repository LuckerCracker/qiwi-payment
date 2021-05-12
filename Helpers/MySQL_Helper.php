<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MySQL_Helper 
{

    /**
     * Создаёт из Array колонки
     * для MySQL запроса
     */
    public static function normalizeColumns($columns_array)
    {
        if($columns_array === '*')
        {
            return $columns_array;
        }

        $columns = [];

        foreach($columns_array as $value)
        {
            array_push($columns, '`'. $value .'`');
        }

        return '(' . implode(',', $columns) . ')';
    }

    /**
     * Создаёт из Array сеты
     * для MySQL запроса
     */
    public static function normalizeSets($sets_array)
    {
        $sets = [];

        foreach($sets_array as $key => $value)
        {
            if( is_array($value) )
            {
                array_push($sets, '`'. $key .'` = '. $value[0] .' '. $value[1] .' \''. static::normalizeInput($value[2]) .'\'');
            }
            else
            {
                array_push($sets, '`'. $key .'` = \''. static::normalizeInput($value) .'\'');
            }
        }

        return implode(', ', $sets);
    }
    
    /**
     * Создаёт из Array значения
     * для MySQL запроса
     */
    public static function normalizeValues($values_array)
    {
        $keys = [];
        $values = [];

        foreach($values_array as $key => $value)
        {
            array_push($keys, '`' . $key . '`');
            array_push($values, '\''. static::normalizeInput($value) .'\'');
        }

        return '(' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
    }

    /**
     * Создаёт из Array колонки
     * для MySQL запроса
     */
    public static function normalizeCreateColums($colums_array)
    {
        $colums = [];

        foreach($colums_array as $key => $value)
        {
            array_push($colums, '`'. $key .'` '. static::normalizeInput($value));
        }

        return implode(' NOT NULL, ', $colums) . ' NOT NULL';
    }

    /**
     * Создаёт из Array колонки и значения
     * для MySQL запроса
     */
    public static function normalizeWhere($where_array)
    {
        $wheres = [];

        foreach($where_array as $key => $value)
        {
            array_push($wheres, '`'. $key .'` = \''. static::normalizeInput($value) .'\'');
        }

        return implode(' AND ', $wheres);
    }

    /**
     * Изменяет символы
     * для безопасного MySQL запроса
     */
    public static function normalizeInput(string $string)
    {
        return preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', $string);
    }
}
?>