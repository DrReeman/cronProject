<?php
/**
 * Created by PhpStorm.
 * User: domrachev
 * Date: 30.08.16
 * Time: 16:23
 */

namespace Parser;


class Parser
{

    private static $keyword = array(
        'minute',
        'hour',
        'day',
        'month',
        'weekday',
        'owner',
        'command',

    );

    static function delEmpty($arr)
    {
        foreach ($arr as $str)
        {
            $newArr[] = rtrim($str);
        }

        $newArr = array_diff($newArr, array(''));
        return $newArr;
    }

    static function getContent($fileContent)
    {

        $environmentVariables = preg_grep('/(?(?=(^([\*]*?(\/\d)*?([\d])*?\s){5}))^$|^.*$)/', $fileContent);
        $rows = preg_grep('/^([\*]*?(\/\d)*?([\d])*?\s){5}/', $fileContent);

        $rows = self::delEmpty($rows);
        $environmentVariables = self::delEmpty($environmentVariables);

        foreach ($rows as $index => $row)
        {
            $explodeRow[$index] = preg_split('/[\s]+/', $row, 7);
        }

        if(!empty($explodeRow))
        {
            foreach ($explodeRow as $index => $rowElement) {
                $cronCommands[$index] = array_combine(self::$keyword, $rowElement);
            }
        }
        else
        {
            $cronCommands = "";
        }

        $newFileContent['environmentVariables'] = $environmentVariables;
        $newFileContent['cronCommands'] = $cronCommands;

        return $newFileContent;
    }

    static function newFileRows($sshConnection, $filePath, $rowGroup) {
        $fileContent = file($filePath);
        $rowsTiming = preg_grep('/^([\*]*?(\/\d)*?\s){5}/', $fileContent);
        $rowsNotTiming = preg_grep('/(?(?=(^([\*]*?(\/\d)*?\s){5}))^$|^.*$)/', $fileContent);
        $newFileRows = array();
        foreach ($rowGroup as $value)
        {
            if (array_key_exists($value, $rowsTiming))
            {
                $newFileRows[$value] = $rowsTiming[$value];
            }
        }

        $newFileRows += $rowsNotTiming;
        ksort($newFileRows);

        return $newFileRows;
       // echo "<pre>";
        //echo var_dump($newFileRows);
        //echo "</pre>";
    }

}