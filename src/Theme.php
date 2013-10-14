<?php

class Theme {

	protected $generator;

	public $name;

	public $versions = array();
	
	protected $basePathes = array();
	
	public function __construct(TemplateGenerator $gen, array $options=array()) {
		$this->generator = $gen;
		$this->setOptions($options);
	}
	
	public function setOptions(array $options) {
		foreach($options as $key => $value) {
			$this->setOption($key, $value);
		}
		return $this;
	}
	
	public function setOption($key, $value=null) {
		switch($key) {
			case 'name':
				$this->name = (string) $value;
				break;
				
			case 'versions':
				$this->versions = $value;
				break;
				
			case 'icons':
				break;
				
			default:
				throw new Exception('Unknown option: ' . $key);
		}
		return $this;
	}
	
	private function importCallback($arg) {
		$basePath = $this->basePathes[0];
		$filename = $arg[1];
		if (!file_exists($filename)) {
			$filename = $basePath . '/' . $filename;
		}
		if (!file_exists($filename)) {
			throw new Exception('Cannot find ' . $arg[0] . ' within path ' . $basePath);
		}
		
		
		return $this->importLessFile($filename, $basePath);
	}
	
	protected function importLessFile($filename, $basePath=null) {
		if ($basePath === null) {
			$basePath = dirname($filename);
		}
		
		array_unshift($this->basePathes, $basePath);
		
		$content = file_get_contents($filename);
		$content = preg_replace_callback('/@import\\s+"([^"]+\\.less)";/', array($this, 'importCallback'),
			$content);
			
		array_shift($this->basePathes);
		
		return $content;
	}

	public function generate($outputFolder, $removeTempFiles=true) {
		$filename = $outputFolder . '/layout.' . $this->name . '.less';
		$compiler = new lessc();
		
		$baseTpl = sprintf("/**\n * %s\n */\n\n", $this->generator->name);
		
		if (!file_exists($outputFolder)) {
			mkdir($outputFolder, 0777, true);
		}
		foreach($this->versions as $version) {
			$layoutfile = realpath($this->name . '/less/' . $version . '.less');
			if (!$layoutfile) continue;
			
			$tmpName = tempnam($outputFolder, 'less');
			$f = fopen($tmpName, 'w');
			fputs($f, $baseTpl);
			fputs($f, $this->importLessFile($layoutfile));
			fclose($f);
			
			
			echo file_get_contents($tmpName);
			
			for($i=0;$i<10;++$i)
				echo PHP_EOL;
			
			if ($removeTempFiles) {
				unlink($tmpName);
			}
		}
	}
}