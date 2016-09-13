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

    public function getCurrentConfigAction()
    {
        $cronConfig = new CronConfig();
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



    public function addFullConfigAction( $args )
    {
        $environment = new Environment(
            $this->setConnection()
        );

        $filePath = $environment->getFilePath(
            $args['sourceConfigId']
        );

        $cronConfig = new CronConfig();

        $cronConfig->createNewConfig(
            $filePath,
            $args
        );
    }

    public function addRowConfigAction( $args )
    {
        $cronConfig = new CronConfig();
        $cronConfig->addRowToFile( $args );
    }

    public function deleteRowConfigAction( $args )
    {
        $cronConfig = new CronConfig();
        $cronConfig->delRowFromFile( $args );
    }

    public function editRowAction( $args )
    {
        $cronConfig = new CronConfig();
        $cronConfig->editRowInFile( $args );
    }

    public function deleteFullConfigAction( $args )
    {
    }

    public function killAction( $args )
    {
    }

    public function runAction( $args )
    {
    }
}