<?php

namespace Controller;

use View\view;
use Model\Environment;
use Model\CronConfig;
use ConnectionDB\DBConnection;

class Controller {

    private function setConnection()
    {
        return new DBConnection();
    }

    public function indexAction()
    {
        $environment = new Environment(
            $this->setConnection()
        );
        $envList = $environment->getEnvironmentList();
        $sourcesList = $environment->getSourcesConfigList();
        $source = array();

        foreach ($sourcesList as $value)
        {
            $source[] = $environment->getSourcesConfigContent( $value );
        }

        $data['envList'] = $envList;
        $data['content'] = $source;
        $view = new View();
        $view->render(
            'index',
            $data
        );
    }

    public function getCurrentConfigAction()
    {
        $cronConfig = new CronConfig();
        $currentConfig = $cronConfig->getCurrentConfigList();
        $source = array();

        foreach ($currentConfig as $value) {
            $source[] = $cronConfig->getCurrentConfigContent( $value );
        }

        $data['content'] = $source;
        $view = new View();
        $view->renderPartial(
            'conf',
            $data
        );
    }



    public function addFullConfigAction( $args )
    {
        $environment = new Environment(
            $this->setConnection()
        );

        $filePath = $environment->getFilePath( $args );

        $cronConfig = new CronConfig();

        $cronConfig->createNewConfig(
            $filePath,
            $args
        );
    }

    public function addRowAction( $args )
    {
        $cronConfig = new CronConfig();
        $data['content'] = $cronConfig->addRowToFile( $args );
        $view = new View();
        $view->renderPartial(
            'addrow',
            $data
        );
    }

    public function saveFileAction( $args ) {
        $cronConfig = new CronConfig();
        $cronConfig->save($args);
    }

    public function delRowAction( $args )
    {
        $cronConfig = new CronConfig();
        $cronConfig->delRowFromFile( $args );

    }

    public function editRowAction( $args )
    {
        $cronConfig = new CronConfig();
        $cronConfig->editRowInFile( $args );
    }

    public function delFullConfigAction( $args )
    {
    }

    public function killAction( $args )
    {
    }

    public function runAction( $args )
    {
    }
}