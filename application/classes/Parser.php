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
        foreach ($arr as $index => $str)
        {
            $newArr[$index] = rtrim($str);
        }

        return $newArr;
    }

    static function getContent($fileContent)
    {
        $flags = self::getFlags($fileContent);

        //$environmentVariables = preg_grep('/(?(?=(^([\*]*?(\/\d)*?([\d])*?\s){5}))^$|^.*$)/', $fileContent);
        $environmentVariables = preg_grep('/^([^\#][\w])/', $fileContent);
        $activeCronCommands = preg_grep('/^([\*]*?(\/\d)*?([\d])*?\s){5}/', $fileContent);
        $inactiveCronCommands = preg_grep('/^([\#])\s?([\*]*?(\/\d)*?([\d])*?\s){5}/', $fileContent);

        $activeCronCommands = self::delEmpty($activeCronCommands);
        $environmentVariables = self::delEmpty($environmentVariables);


        foreach ($activeCronCommands as $index => $row)
        {
            $explodeRow[$index] = preg_split('/[\s]+/', $row, 7);
        }

        foreach ($inactiveCronCommands as $index => $row)
        {
            $row = ltrim($row, '#');
            $row = ltrim($row);
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

        ksort($cronCommands);

        //echo "<pre>";
        //echo var_dump($inactiveCronCommands);
        //echo "</pre>";

        $newFileContent['environmentVariables'] = $environmentVariables;
        $newFileContent['cronCommands'] = $cronCommands;
        $newFileContent['flags'] = $flags;

        return $newFileContent;
    }

    static function newFileRows($sshConnection, $filePath, $rowGroup) {
        $fileContent = file($filePath);
        $rowsTiming = preg_grep('/^([\*]*?(\/\d)*?\s){5}/', $fileContent);
        $environmentVars = preg_grep('/(?(?=(^([\*]*?(\/\d)*?\s){5}))^$|^.*$)/', $fileContent);
        $comments = preg_grep('/(?(?=(^([\#]*?))^$|^.*$)/', $fileContent);


        echo $comments;

        $newFileRows = array();
        foreach ($rowGroup as $value)
        {
            if (array_key_exists($value, $rowsTiming))
            {
                $newFileRows[$value] = $rowsTiming[$value];
            }
        }

        $newFileRows += $environmentVars;
        ksort($newFileRows);

        //return $newFileRows;
       // echo "<pre>";
        //echo var_dump($newFileRows);
        //echo "</pre>";
    }

    static function getFlags($fileContent)
    {
        foreach ($fileContent as $row)
        {

            if (preg_match('/^$/', $row, $matches))
            {
                $flags[] = 'emptyRow';
            }

            if (preg_match('/^(\#)\s?([\w\d])/', $row, $matches))
            {
                $flags[] = 'comment';
            }

            if (preg_match('/^([\#])\s?([\*]*?(\/\d)*?([\d])*?\s){5}/', $row, $matches))
            {
                $flags[] = 'inactiveCronCommand';
            }

            if (preg_match('/^([^\#][\w])/', $row, $matches))
            {
                $flags[] = 'environmentVar';
            }

            if (preg_match('/^([\*]*?(\/\d)*?([\d])*?\s){5}/', $row, $matches))
            {
                $flags[] = 'activeCronCommand';
            }

        }

        return $flags;
    }

}