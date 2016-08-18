<?php

class MemStatus
{
    private $__mem_prev = 0;
    private $__output_path = null;
    private $__file_name = '/mem.txt';
    private $__step = 1;
    private $__name = '';
    private $__rec = array();

    public function __construct($name='', $out_put_path=null){
		$this->__output_path = $out_put_path;
		$this->__name = $name;
		$this->__rec[] = '===============Start: '.$this->__name.'=================';
    }
    
    public function rec($step=null) {
    	$msg = (isset($step)?$step:$this->__step++).'==>Delta: '.(memory_get_usage() - $this->__mem_prev).'  Used: '.($this->__mem_prev = memory_get_usage());
		$this->__rec[] = $msg;
    }
    
	public function output(){
		$this->__rec[] = 'Max: '.memory_get_peak_usage();
		$this->__rec[] = '===============Over: '.$this->__name.'=================';
		if (isset($this->__output_path)) {
			if(!file_exists($this->__output_path)){
				mkdir($this->__output_path, 0777, true);
			}
			file_put_contents($this->__output_path.$this->__file_name, implode(PHP_EOL, $this->__rec).PHP_EOL, FILE_APPEND);
		}else {
			echo implode('<br/>', $this->__rec);;
		}
	}
}

?>