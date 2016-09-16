<?php

namespace Connection;


class SSHConnection {

    private $connection;
    const PATH = '/etc/cron.d/';


    public function __construct($host, $port, $userName, $userPass)
    {

        try
	    {
	        if (is_null($host) || is_null($port) || is_null($userName) || is_null($userPass))
                throw new \Exception("Please specify the host, port, username and password!");
            $this->connection = ssh2_connect($host, $port);

            if ( ! $this->connection)
                throw new \Exception("The SSH2 connection could not be established.");

            $authentication = ssh2_auth_password($this->connection, $userName, $userPass);

            if ( ! $authentication)
                throw new Exception("Could not authenticate '{$userName}' using password: '{$userPass}'.");

        } catch(Exception $e) {
                $this->errorMessage($e->getMessage());
        }

    }


    public function getFileList()
    {
        $sftp = ssh2_sftp($this->connection);
        return scandir('ssh2.sftp://' . $sftp . self::PATH);

    }

    public function getFileContent( $fileName )
    {
        $sftp = ssh2_sftp($this->connection);
        return file('ssh2.sftp://' . $sftp . self::PATH . $fileName );
    }

    public function createNewFile($args)
    {
        //$command = 'echo > ' . self::PATH . $args['fileName'];
        //echo $command;
        $stream = ssh2_exec($this->connection, $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        echo stream_get_contents($stream_out);


        //$command = 'cat' . self::PATH . $args['fileName'];
        //$command = 'cat' . self::PATH . 'osk.conf';
        //$result = ssh2_exec($this->connection, $command);
        //echo $result;
        //$files = self::getFileList();
       // foreach ($files as $file) {
        //    echo $file;
       // }
        //$stream = fopen('ssh2.sftp://' . $sftp . self::PATH . $args['fileName'], 'w+');
        //fwrite($stream, $args['content']);
        //fclose($stream);
        //$result = file_put_contents( 'ssh2.sftp://' . $sftp . self::PATH . $args['fileName'], $args['content']);
       // $message = ((bool)$result) ? 'Файл успешно сохранен!' : 'Произшла ошибка!';
       // return $message;
    }

    public function exec()
    {

    }

    public function writeToFile()
    {

    }

    public function removeFile()
    {

    }


/*
    public function append_cronjob()
    {

    }

    public function remove_cronjob()
    {

    }

    public function remove_crontab()
    {

    }

    private function crontabFileExists()
    {

    }
*/
    private function errorMessage()
    {

    }

} 