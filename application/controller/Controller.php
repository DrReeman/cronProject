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
            $source[] = $environment->getSourcesConfigContent(
                $value
            );
        }

        $data1['envList'] = $envList;
        $data1['content'] = $source;
        $view = new View();
        $view->render(
            'index',
            $data1
        );
    }

    public function getCurrentConfigAction( $args )
    {
        $cronConfig = new CronConfig(
            $args['environmentId'],
            $this->setConnection()
        );
        $currentConfig = $cronConfig->getCurrentConfigList();
        $source = array();

        foreach ($currentConfig as $value) {
            $source[] = $cronConfig->getCurrentConfigContent(
                $value
            );
        }

        $data1['content'] = $source;
        $view = new View();
        $view->renderPartial(
            'conf',
            $data1
        );
    }

    public function editRowAction( $args )
    {
        $cronConfig = new CronConfig (
            $args['environmentId'],
            $this->setConnection()
        );
        $filePath = $cronConfig->getFilePath(
            $args['configId']
        );
        $cronConfig->editRowInFile(
            $filePath,
            $args['rowIndex'],
            $args['cronTiming']
        );
    }

    public function addFullConfigAction( $args )
    {
        $environment = new Environment(
            $this->setConnection()
        );

        $filePath = $environment->getFilePath(
            $args['sourceConfigId']
        );

        echo  $args['environmentId'];

        $cronConfig = new CronConfig(
            $args['environmentId'],
            $this->setConnection()
        );

        $cronConfig->createNewConfig(
            $filePath,
            $args['rowGroup']
        );
    }

    public function addRowConfigAction( $args )
    {
        $cronConfig = new CronConfig(
            $args['environmentId'],
            $this->setConnection()
        );
        $filePath = $cronConfig->getFilePath(
            $args['configId']
        );
        $cronConfig->addRowToFile(
            $filePath,
            $args['cronTiming']
        );
    }

    public function deleteFullConfigAction( $args )
    {
    }

    public function deleteRowConfigAction( $args )
    {
        $cronConfig = new CronConfig(
            $args['environmentId'],
            $this->setConnection()
        );
        $filePath = $cronConfig->getFilePath(
            $args['configId']
        );
        $cronConfig->delRowFromFile(
            $filePath, $args['rowIndex']
        );
    }

    public function killAction( $args )
    {
    }

    public function runAction( $args )
    {
    }
}