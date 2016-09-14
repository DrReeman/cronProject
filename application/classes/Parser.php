<?php
/**
 * Created by PhpStorm.
 * User: domrachev
 * Date: 30.08.16
 * Time: 16:23
 */

namespace Parser;


class Parser {

    private static $keyword = array(
        'minute',
        'hour',
        'day',
        'month',
        'weekday',
        'owner',
        'process',
        'command',

    );

    static function delEmpty($arr) {
        foreach ($arr as $str) {
            $newArr[] = rtrim($str);
        }

        $newArr = array_diff($newArr, array(''));
        return $newArr;
    }

    static function getContent( $directory ) {
        $fileContent = file( $directory );

        $environmentVariables = preg_grep( '/(?(?=(^([\*]*?(\/\d)*?\s){5}))^$|^.*$)/', $fileContent );
        $rows = preg_grep( '/^([\*]*?(\/\d)*?\s){5}/', $fileContent );

        //echo $directory;

        foreach ( $rows as $index => $row ) {
            //echo $row,"<br>";
            $explodeRow[$index] = preg_split( '/[\s]+/', $row, 8 );
        }

        foreach ($explodeRow as $index => $rowElement) {
            $cronCommands[$index] = array_combine( self::$keyword, $rowElement );
        }

        $environmentVariables = self::delEmpty($environmentVariables);


        $newFileContent['environmentVariables'] = $environmentVariables;
        $newFileContent['cronCommands'] = $cronCommands;
        echo "<pre>";
        echo var_dump($explodeRow);
        echo "</pre>";
        return $newFileContent;
    }

    static function newFileRows( $filePath, $rowGroup ) {
        $fileContent = file($filePath);
        $rowsTiming = preg_grep( '/^([\*]*?(\/\d)*?\s){5}/', $fileContent );
        $rowsNotTiming = preg_grep( '/(?(?=(^([\*]*?(\/\d)*?\s){5}))^$|^.*$)/', $fileContent );
        $newFileRows = array();
        foreach ( $rowGroup as $value ) {
            if ( array_key_exists( $value, $rowsTiming ) ) {
                $newFileRows[$value] = $rowsTiming[$value];
            }
        }

        $newFileRows += $rowsNotTiming;
        ksort( $newFileRows );

        return $newFileRows;
       // echo "<pre>";
        //echo var_dump($newFileRows);
        //echo "</pre>";
    }

}