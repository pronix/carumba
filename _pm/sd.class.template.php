<?

class Template{
	var $respond;

	function Template($path, $object = 0){
		ob_start();
		require('tpl/'.$path.'.php');
		$this->respond = ob_get_contents();
		ob_end_clean();
	}

	function getContent(){
		return $this->respond;
	}
}

?>