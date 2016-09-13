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


    static function getContent( $directory ) {
        $fileContent = file( $directory );
        $rows = preg_grep( '/^([\*]*?(\/\d)*?\s){5}/', $fileContent );

        foreach ( $rows as $index => $row ) {
            $explodeRow[$index] = preg_split( '/[\s]+/', $row, 8 );
        }

        foreach ($explodeRow as $index => $rowElement) {
            $newFileContent[$index] = array_combine( self::$keyword, $rowElement );
        }
        //echo "<pre>";
        //echo var_dump($newFileContent);
        //echo "</pre>";

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