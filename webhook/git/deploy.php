<?php

class Deploy {

    /**
     * A callback function to call after the deploy has finished.
     *
     * @var callback
     */
    public $post_deploy;

    /**
     * The name of the file that will be used for logging deployments. Set to
     * FALSE to disable logging.
     *
     * @var string
     */
    private $_log = 'deploy.log';

    /**
     * The timestamp format used for logging.
     *
     * @link    http://www.php.net/manual/en/function.date.php
     * @var     string
     */
    private $_date_format = 'd/m/Y H:i:s';

    /**
     * The path to git
     *
     * @var string
     */
    private $_git_bin_path ;

    /**
     * The directory where your git repository is located, can be
     * a relative or absolute path from this PHP script on server.
     *
     * @var string
     */
    private $_directory;

    /**
     * The directory where your git work directory is located, can be
     * a relative or absolute path from this PHP script on server.
     *
     * @var string
     */
    private $_work_dir;

    /**
     * Sets up defaults.
     *
     * @param  array   $option       Information about the deployment
     */
    public function __construct($options = array())
    {

        $available_options = array('directory', 'work_dir', 'log', 'date_format', 'branch', 'remote', 'syncSubmodule', 'git_bin_path');

        foreach ($options as $option => $value){
            if (in_array($option, $available_options)) {

                if ($option == 'directory' || $option == 'work_dir') {
                    if(substr($value, -1)!='/')
                        $value = $value.'/';
                }

                $this->{'_'.$option} = $value;
            }
        }
        if (empty($this->_work_dir)) {
            $this->_work_dir = $this->_directory;
            //$this->_directory = $this->_directory . '/.git';
        }

        $this->log('Attempting deployment...');
        $this->log('Web Directory: ' . $this->_directory);
        $this->log('Local repository: ' . $this->_work_dir);
    }

    /**
     * Writes a message to the log file.
     *
     * @param  string  $message  The message to write
     * @param  string  $type     The type of log message (e.g. INFO, DEBUG, ERROR, etc.)
     */
    public function log($message, $type = 'INFO')
    {
        if ($this->_log) {

            $filename = $this->_log;                // Set the name of the log file

            if ( ! file_exists($filename)) {
                file_put_contents($filename, '');   // Create the log file
                chmod($filename, 0666);             // Allow anyone to write to log files
            }

            // Write the message into the log file
            file_put_contents($filename, '['.date($this->_date_format).']' .' ['.$type.']: '.$message.PHP_EOL, FILE_APPEND);
        }
    }

    /**
     *
     * Executes the necessary commands to deploy .
     *
     */
    public function execute()
    {
        $commands = array(
            $this->_git_bin_path.' --git-dir='.$this->_work_dir.'.git'.' --work-tree='.$this->_work_dir.' reset --hard HEAD 2>&1',
            $this->_git_bin_path.' --git-dir='.$this->_work_dir.'.git'.' --work-tree='.$this->_work_dir.' status 2>&1',
            $this->_git_bin_path.' --git-dir='.$this->_work_dir.'.git'.' --work-tree='.$this->_work_dir.' pull 2>&1', // Update the local repository
            'cd ' . $this->_directory,
            $this->_git_bin_path.' --git-dir='.$this->_work_dir.'.git'.' --work-tree='.$this->_directory.' checkout -f 2>&1', // Checking out to web directory
        );

        try {

            foreach($commands AS $command){
                $output = shell_exec($command);
                $this->log($command);
                $this->log($output);
            }

            $this->log('Deployment successful.');

        } catch (Exception $e) {
            $this->log($e, 'ERROR');
        }
    }
}