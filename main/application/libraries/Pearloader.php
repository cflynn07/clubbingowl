<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//http://codeigniter.com/wiki/PEAR_integration
class Pearloader{
	function load($package, $subpackage, $class, $options = null){
			
		require_once($package . '/' . $subpackage .  '/' . $class . '.php');
		
		$classname = $package . '_' . $subpackage . '_' . $class;
		
   		if(is_null($options)){
   			
			return new $classname();
			
		}else{
			
			return new $classname($options);
			
		}
	}
}

/* End of file Pearloader.php */
/* Location: ./application/libraries/Pearloader.php */