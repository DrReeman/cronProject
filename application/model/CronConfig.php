<?php

namespace Model;

use Parser\Parser;

class CronConfig {

    const PATH = '/www/CronProject1/cron.d/';

    public function getCurrentConfigList() {
        $files = array_diff( scandir( self::PATH ), array( '..', '.' ) );
        return $files;
    }

    public function getCurrentConfigContent( $fileName )
    {
        $directory = self::PATH . $fileName;
        $configContent = Parser::getContent( $directory );
        $current['configName'] = $fileName;
        $current['directory'] = $directory;
        $current['configContent'] = $configContent;
        return $current;
    }

    public function addRowToFile( $args ) {
        $row = implode( ' ', $args['cronTiming'] ) . PHP_EOL;
        file_put_contents( self::PATH . $args['configName'], $row, FILE_APPEND );
    }

    public function delRowFromFile( $args )
    {
        $directory = self::PATH . $args['configName'];

        if ( $args['rowIndex'] != '' ) {
            $file=file( $directory );

            for( $i=0;$i<sizeof( $file );$i++ )
                if( $i==$args['rowIndex'] )
                    unset( $file[$i] );

            file_put_contents( $directory, implode( '', $file ) );
        }
    }

    public function editRowInFile( $args )
    {
        $directory = self::PATH . $args['configName'];
        $row = implode( ' ', $args['cronTiming'] ) . PHP_EOL;

        if ( $args['rowIndex'] != '' ) {
            $file=file( $directory );

            for( $i=0;$i<sizeof( $file );$i++ )
                if( $i==$args['rowIndex'] )
                    $file[$i] = $row;

            file_put_contents( $directory, implode( '', $file ) );
        }
    }

    public function createNewConfig( $filePath, $args )
    {
        $fileRows = Parser::newFileRows( $filePath, $args['rowGroup'] );
        $explodeFilePath = explode( '/', $filePath );
        $len = count( $explodeFilePath );
        $fileName = $explodeFilePath[$len-1];
        $row = implode( '', $fileRows ) . PHP_EOL;
        file_put_contents( self::PATH . $fileName, $row );
    }

}