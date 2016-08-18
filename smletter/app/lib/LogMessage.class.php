<?php
class LogMessage
{
    private $__message;
    private $__param;
    private $__file;
    private $__fullname;
    private $__line;
    private $__function;
    private $__args;
    private $__class;
	private $__level;

    public function __construct($message, $level='NOLEVEL', $param=null)
    {
		$this->__level = $level;
        $this->__message = $message;
        $this->getDebugInfo();
        $this->__param = $param?$param:null;
    }

    //+----------------------------------------------
    //取得类内调试信息

    private function getDebugInfo()
    {
        if (function_exists('debug_backtrace'))
        {
            $trace = debug_backtrace();
            $info  = $trace[2];
            if (!$info)
            {
                return;
            }

            $this->__fullname = $info['file'];
            $this->__file     = basename($this->__fullname);
            $this->__line     = $info['line'];
            $this->__function = $info['function'];
            $this->__class    = $info['class'];
            if ($info['args'])
            {
                foreach ($info['args'] as $k=>$v) 
                {
                    $this->__args .= $k."=".$v;
                }
            } 
            else
            {
                $this->__args = null;
            }
        }
        else
        {
            $this->__fullname = '';
            $this->__file     = '';
            $this->__line     = '';
            $this->__function = '';
            $this->__class    = '';
            $this->__args     = '';
        }
    }

    //+-----------------------------------------------
    //返回调试信息
    public function getMessage()
    {
		$date = date("Y-m-d H:i:s");
        if (is_array($this->__message))
        {
            $return = "Array[";
            foreach ($this->__message as $k=>$v)
            {
                $return .= "{$k}=>{$v} ";
            }
            $return .= "]".PHP_EOL;
        }
        else if (is_object($this->__message))
        {
            $return = "Object:".get_class($this->__message)."[";
            foreach (get_object_vars($this->__message) as $k=>$v)
            {
                $return .= "{$k}=>{$v} ";
            }
            $return .= "]".PHP_EOL;
        }
        else
        {
            $return = $this->__message.PHP_EOL;
        }
        return $date.LOG_SEP.$this->__level.LOG_SEP.$return;
    }

    //+--------------------------------------------
    //写信息到log文件
    public function write($filename)
    {
        if (function_exists('file_put_contents'))
        {
            $message = $this->getMessage();
            file_put_contents($filename, $message, FILE_APPEND|LOCK_EX);
        }
        else
        {
            $fp = @fopen($filename, '+a');
            @flock($fp, LOCK_EX);
            @fwrite($fp, $message);
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
    }

    public function __get($key)
    {
        return $this->$key;
    }

    //+----------------------------------------------
    //取得log类内属性
    public function getLogVar($key)
    {
        return $this->__get($key);
    }
}
?>
