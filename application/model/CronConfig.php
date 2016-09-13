<?php

namespace Model;

use Parser\Parser;
use PDO;

class CronConfig {
    private $environmentId;
    private $configs = array();
    private $connection;
   const PATH = '/www/CronProject1/cron.d/';

    public function __construct( $environmentId, $connection )
    {
        $this->environmentId = $environmentId;
        $this->connection = $connection;
    }

    public function getCurrentConfigList() {
        $configListQuery = 'SELECT *
                            FROM CurrentConfig
                            WHERE envID = :envId';
        $sth = $this->connection->prepare( $configListQuery );
        $sth->bindParam( ':envId', $this->environmentId, PDO::PARAM_STR );
        $sth->execute();
        $this->configs = $sth->fetchAll( PDO::FETCH_ASSOC );
        return $this->configs;
    }

    public function getCurrentConfigContent( $args )
    {
        $filename = $args['configName'] . '.' . $args['configExtension'];
        $directory = self::PATH . $filename;
        $configContent = Parser::getContent( $directory );
        $current['configName'] = $filename;
        $current['configId'] = $args['configId'];
        $current['configContent'] = $configContent;
        return $current;
    }

    public function getFilePath( $configId ) {
        $configQuery = 'SELECT configName, configExtension
                        FROM CurrentConfig
                        WHERE configId = :confId';
        $sth = $this->connection->prepare( $configQuery );
        $sth->bindParam( ':confId', $configId, PDO::PARAM_STR );
        $sth->execute();
        $filename = $sth->fetch( PDO::FETCH_ASSOC );
        return $filename['configName'] . '.' . $filename['configExtension'];
    }

    public function addRowToFile( $filePath, $param ) {
        $row = implode( ' ', $param ) . PHP_EOL;
        file_put_contents( self::PATH . $filePath, $row, FILE_APPEND );
    }

    public function delRowFromFile( $filePath, $rowIndex ) {
        $directory = self::PATH . $filePath;
        if ( $rowIndex != '' ) {
            $file=file( $directory );
            for( $i=0;$i<sizeof( $file );$i++ )
                if( $i==$rowIndex ) unset( $file[$i] );
            $fp=fopen( $directory,'w' );
            fputs( $fp,implode( '',$file ) );
            fclose( $fp );
        }
    }

    public function editRowInFile( $filePath, $rowIndex, $cronTiming ) {
        $directory = self::PATH . $filePath;
        $row = implode( ' ', $cronTiming ) . PHP_EOL;

        if ( $rowIndex != '' ) {
            $file=file( $directory );
            for( $i=0;$i<sizeof( $file );$i++ )
                if( $i==$rowIndex ) $file[$i] = $row;
            $fp=fopen( $directory,'w' );
            fputs( $fp,implode( '',$file ) );
            fclose( $fp );
        }
    }

    public function createNewConfig( $filePath, $rowGroup ) {
        $fileRows = Parser::newFileRows( $filePath, $rowGroup );
        $explodeFilePath = explode( '/', $filePath );
        $len = count( $explodeFilePath );
        $fileNameExt = $explodeFilePath[$len-1];
        $explodeFileNameExt = explode( '.', $fileNameExt );
        $fileName = $explodeFileNameExt[0];
        $fileExt = $explodeFileNameExt[1];

        $queryInsert = 'INSERT
                        INTO CurrentConfig(configName, configExtension, envId)
                        VALUES  (:confName, :ext, :id)';

        $sth = $this->connection->prepare( $queryInsert );
        try {
            $this->connection->beginTransaction();
            $sth->bindParam( ':confName', $fileName );
            $sth->bindParam( ':ext', $fileExt );
            $sth->bindParam( ':id', $this->environmentId );
            $resultReplace = $sth->execute();
            $this->connection->commit();
        } catch ( \PDOException $e ) {
            $this->connection->rollback();
            echo 'Database error: ' . $e->getMessage();
            die();
        }

        if( $resultReplace ) {
            $row = implode( ' ', $fileRows ) . PHP_EOL;
            file_put_contents( self::PATH . $fileNameExt, $row );
        }

    }

}