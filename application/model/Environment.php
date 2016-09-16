<?php

namespace Model;

use PDO;
use Parser\Parser;

class Environment
{

    private $connection;
    private $configs = array();
    private $environmentList = array();

    public function __construct( $connection )
    {
        $this->connection = $connection;
    }

    public function getEnvironmentList()
    {
        $environmentQuery = 'SELECT *
                             FROM Environment';
        $sth = $this->connection->prepare( $environmentQuery );
        $sth->execute();

        $this->environmentList = $sth->fetchAll( PDO::FETCH_ASSOC );
        return $this->environmentList;
    }

    public function getSourcesConfigList()
    {
        $SourceQuery = 'SELECT *
                        FROM SourcesConfig';
        $sth = $this->connection->prepare( $SourceQuery );
        $sth->execute();
        $this->configs = $sth->fetchALL( PDO::FETCH_ASSOC );
        return $this->configs;
    }

    public function getSourcesConfigContent( $args )
    {
        $filename = $args['configName'] . '.' . $args['configExtension'];
        $fullDirectory = $args['directory'] . $filename;
        $fileContent = file( $fullDirectory );
        //echo $fullDirectory;
        $configContent = Parser::getContent( $fileContent );
        $source['sourceName'] = $filename;
        $source['configId'] = $args['configId'];
        $source['sourceContent'] = $configContent;
        return $source;
    }


    public function getFilePath( $args )
    {
        $SourceQuery = 'SELECT directory, configName, configExtension
                        FROM SourcesConfig
                        where configId = :confId';
        $sth = $this->connection->prepare( $SourceQuery );
        $sth->bindParam( ':confId', $args['sourceConfigId'], PDO::PARAM_STR );
        $sth->execute();
        $filename = $sth->fetch( PDO::FETCH_ASSOC );
        return $filename['directory'] . $filename['configName'] . '.' . $filename['configExtension'];
    }
}