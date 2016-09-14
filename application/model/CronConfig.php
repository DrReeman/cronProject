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
        //echo "<pre>";
       // echo var_dump($current['configContent']);
       // echo "</pre>";die;
        return $current;
    }

    public function addRowToFile( $args )
    {
        if ( count($args['cronTiming']) == 8 ) {
            return $args['cronTiming'];
        } else {
          return;
        }
    }

    public function save( $args )
    {
        //$explodeContent = $args['environVarsConfig'] + $args['cronCommandsConfig'];
        foreach($args['environVarsConfig'] as $val) {
            $environVarsConfig[] = implode('', $val)  . PHP_EOL;
        }
        $content[] = implode('', $environVarsConfig);

        foreach($args['cronCommandsConfig'] as $val) {
            $cronCommandsConfig[] = implode(' ', $val)  . PHP_EOL;
        }
        $content[] = implode('', $cronCommandsConfig)  . PHP_EOL;

        $content = implode(PHP_EOL, $content)  . PHP_EOL;

        $directory = self::PATH . $args['currentConfigName'];
        //$file=file( $directory );
        //file_put_contents( $directory, $content );
        //$content = $environVarsConfig + $cronCommandsConfig;
        //echo "<pre>";
        // echo var_dump($content);
       //  echo "</pre>";die;
        //$content = implode(' ');
    }

    public function delRowFromFile( $args )
    {
        $directory = self::PATH . $args['configName'];

        if ( $args['rowIndex'] != '' ) {
            $file=file( $directory );

            for( $i=0;$i<sizeof( $file );$i++ )
                if( $i==$args['rowIndex'] )
                    unset( $file[$i] );

            //file_put_contents( $directory, implode( '', $file ) );
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