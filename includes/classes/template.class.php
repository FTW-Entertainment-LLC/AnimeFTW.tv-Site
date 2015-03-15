 <?php
########################################################
# Template Constructor Class for AnimeFTW.tv
# Copyright 2008-2014, FTW Entertainment LLC
# 	~Written by Brad Riemann~
# These sets of classes were developed for the
# the specific page setup for AnimeFTW.tv
########################################################

class Template {
    protected $file;
    protected $values = array();
  
	public function __construct($file)
	{
		$this->file = $file;
	}
	public function set($key, $value)
	{
		$this->values[$key] = $value;
	}
	
	static public function merge($templates, $separator = " ")
	{
		$output = "";
		
		foreach ($templates as $template)
		{
			$content = (get_class($template) !== "Template") 
				? "Error, incorrect type - expected Template."
				: $template->output();
			$output .= $content . $separator;
		}
		
		return $output;
	}
  
	public function output()
	{
		if (!file_exists($this->file))
		{
			return "Error loading template file ($this->file).";
		}
		$output = file_get_contents($this->file);
		
		foreach ($this->values as $key => $value)
		{
			$tagToReplace = "[@$key]";
			$output = str_replace($tagToReplace, $value, $output);
		}
		return $output;
	}
}