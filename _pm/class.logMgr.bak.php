<?
/*
    Log Manager
*/

    define("LOG_PRESENT", 1);

    class LogManager
    {
        var $logFileName;
        var $fd;

        function LogManager($logFile)
        {
            $this->logFileName = $logFile;
            $this->fd = fopen($this->logFileName, "a+");
        }

        function writeLine($errno, $errstr, $errfile, $errline)
        {
            if ($this->fd)
                fwrite($this->fd,  "["  . strftime(GetCfg("DateFormat")) . "] $errfile:$errline - $errstr\n");
        }

        function LogManagerDestroy()
        {
            if ($this->fd)
            {
                fclose($this->fd);
            }
        }
    }
?>