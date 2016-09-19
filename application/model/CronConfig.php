<?php

namespace Model;

use Parser\Parser;
use Connection\SSHConnection;

class CronConfig {

      // private $host = '192.168.215.227';
      // private $port = 22;
      // private $userName = 'developer';
      // private $userPass = 'developer';


    private $host = '192.168.191.221';
    private $port = 22;
    private $userName = 'root';
    private $userPass = 'reemanintegral1994';

    public function getCurrentConfigList() {
        $connection = new SSHConnection($this->host, $this->port, $this->userName, $this->userPass);
        $files = $connection->getFileList();
        $files = array_diff( $files, array( '..', '.' ) );

        return $files;
    }

    public function getCurrentConfigContent( $fileName )
    {
        $connection = new SSHConnection($this->host, $this->port, $this->userName, $this->userPass);
        $fileContent = $connection->getFileContent( $fileName );
        $configContent = Parser::getContent( $fileContent );
        $current['configName'] = $fileName;
        $current['configContent'] = $configContent;

        return $current;
    }



    public function addRowToFile( $args )
    {
        if ( !empty($args['data']) ) {
                return $args['data'];
        } else {
            return false;
        }
    }

    public function save( $args )
    {
        foreach($args['environVarsConfig'] as $val) {
            $environVarsConfig[] = implode('', $val)  . PHP_EOL;
        }

        foreach($args['cronCommandsConfig'] as $val) {
            $cronCommandsConfig[] = implode(' ', $val)  . PHP_EOL;
        }

        $content[] = implode('', $environVarsConfig);
        $content[] = implode('', $cronCommandsConfig)  . PHP_EOL;

        $content = implode(PHP_EOL, $content);

        $connection = new SSHConnection($this->host, $this->port, $this->userName, $this->userPass);
        $args['content'] = $content;
        $args['fileName'] = $args['currentConfigName'];
        $message = $connection->createNewFile( $args );
        echo $message;

    }
/*
    public function delRowFromFile( $args )
    {
        $sftp = ssh2_sftp($this->sshConnection);
        $directory = 'ssh2.sftp://' . $sftp . self::FILEPATH . $args['configName'];
        //$directory = self::FILEPATH . $args['configName'];

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
        $sftp = ssh2_sftp($this->sshConnection);
        $directory = 'ssh2.sftp://' . $sftp . self::FILEPATH . $args['configName'];
        //$directory = self::FILEPATH . $args['configName'];
        $row = implode( ' ', $args['cronTiming'] ) . PHP_EOL;

        if ( $args['rowIndex'] != '' ) {
            $file=file( $directory );

            for( $i=0;$i<sizeof( $file );$i++ )
                if( $i==$args['rowIndex'] )
                    $file[$i] = $row;

            file_put_contents( $directory, implode( '', $file ) );
        }
    }

*/


    public function removeConfig( $args )
    {
        $connection = new SSHConnection($this->host, $this->port, $this->userName, $this->userPass);
        $message = $connection->removeFile( $args );
        echo $message;
    }

}