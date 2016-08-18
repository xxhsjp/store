<?php
/**
*Log::debug(basename(__FILE__).':'.__METHOD__.'@'.__LINE__.LOG_SEP);
*Log::error(__METHOD__.'@'.__LINE__.LOG_SEP);
*/
class Log
{
    const DEBUG = 1;
	const INFO = 2;
    const WARN = 4;
    const ERROR = 8;
	const OUTPUTLEVEL = LOG_OUTPUT_LEVEL;
    const LOGPATH = LOG_OUTPUI_PATH;
	const SYS_LOG = LOG_OUTPUT_SYS_FILE;
	const OPER_LOG = LOG_OUTPUT_OPER_FILE;

    //+----------------------------------------------
    //判断日志类型
    private static function __isOutputLevel($lv)
    {
        return (self::OUTPUTLEVEL & $lv) != 0;
    }
    //+-----------------------------------------------------
    //记录日志
    private static function write_log($message, $lv, $lvName, $file = null)
    {
        if (self::__isOutputLevel($lv))
        {
			$file = empty($file) ? self::SYS_LOG : $file;
			if(strcasecmp($file, 'CLOSE') === 0){
				return false;
			}
            $log = new LogMessage($message, $lvName);
            $log->write(self::LOGPATH.$file);
        }
        return true;
    }

    public static function level($lv=Log::DEBUG)
    {
    	return self::__isOutputLevel($lv);
    }
    
    public static function debug($message)
    {
        self::write_log($message, self::DEBUG, 'DEBUG');
    }

	public static function info($message)
    {
        self::write_log($message, self::INFO, 'INFO');
    }

    public static function warn($message)
    {
        self::write_log($message, self::WARN, 'WARN');
    }

    public static function error($message)
    {
        self::write_log($message, self::ERROR, 'ERROR');
    }

	public static function oper_log($message)
    {
        self::write_log($message, 15, 'OPER', self::OPER_LOG);
    }

	public static function w_log($message, $file)
    {
        self::write_log($message, 15, 'REC', $file);
    }
}
?>
