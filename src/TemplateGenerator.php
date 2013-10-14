<?php

require_once __DIR__ . '/Author.php';
require_once __DIR__ . '/Theme.php';

class TemplateGenerator {

	public $name;
	
	public $authors = array();
	
	public $themes = array();

	public function __construct(array $options=array()) {
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
				
			case 'author':
				$this->setAuthors(array($value));
				break;
				
			case 'authors':
				$this->setAuthors($value);
				break;
				
			case 'themes':
				$this->setThemes($value);
				break;
				
			default:
				throw new Exception('Unknown option: ' . $key);
		}
		return $this;
	}
	
	public function setAuthors(array $authors) {
		$this->clearAuthors();
		foreach($authors as $author) {
			$this->addAuthor($author);
		}
		return $this;
	}
	
	public function clearAuthors() {
		$this->authors = array();
		return $this;
	}
	
	public function addAuthor($author) {
		$obj = null;
		if (is_object($author) && $author instanceof Author) {
			$obj = $author;
		} elseif (is_array($author)) {
			$obj = new Author($author);
		}
		
		if ($obj === null) {
			throw new Exception('Given value is not an author');
		}
		$this->authors[] = $obj;
		return $this;
	}
	
	public function setThemes(array $themes) {
		return $this->clearThemes()->addThemes($themes);
	}
	
	public function clearThemes() {
		$this->themes = array();
		return $this;
	}
	
	public function addThemes(array $themes) {
		foreach($themes as $name => $options) {
			$this->addTheme($name, $options);
		}
		return $this;
	}
	
	public function addTheme($name, $options) {
		$theme = null;
		if (is_object($options) && $options instanceof Theme) {
			$theme = $options;
		} elseif(is_array($options)) {
			$options['name'] = $name;
			$theme = new Theme($this, $options);
		}
		
		if ($theme === null) {
			throw new Exception('Invalid Theme value');
		}
		
		$this->themes[$name] = $theme;
		
		return $this;
	}
	
	public function generate($outputFolder, $removeTempFiles=true) {
		foreach($this->themes as $theme) {
			$theme->generate($outputFolder . DIRECTORY_SEPARATOR . $this->name,
				$removeTempFiles);
		}
	}
	
	public static function createFromFile($filename) {
		$data = spyc_load_file($filename);
		return isset($data['layout']) && is_array($data['layout'])
			? new TemplateGenerator($data['layout'])
			: null;
	}

}