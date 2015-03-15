<?php
/****************************************************************\
## FileName: mobile.class.php									 
## Author: Brad Riemann										 
## Usage: Mobile Page Constructor for the Mobile Site
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Mobile extends Config {
	
	var $Page; // The current page of the data being given.
	var $Type; // the type of the page is parsed out, so that we can get the correct template.
	
	public function __construct($Page = 'home')
	{
		parent::__construct();
		$this->Page = $Page;
	}
	
	public function DisplayPage()
	{
		$this->ParseType();
	}
	
	private function ParseType()
	{
		if(substr($this->Page,0,5) == "anime")
		{
			// if the page has "anime" in the start
			$Anime = substr($this->Page,6);
			
			// call the anime class, please and thank you.
			require_once("anime.class.php");
			$Anime = new Anime($Anime);
			$Anime->Init();
		}
		else if(substr($this->Page,0,7) == "profile" || substr($this->Page,0,4) == "edit")
		{
			// if the page has "profiel" or "edit" in the start, it is a page destined
			
			// call the profile class, please and thank you.
			require_once("profile.class.php");
			$Profile = new Profile();
			$Profile->Init();
		}
		else if(substr($this->Page,0,5) == "forum")
		{
			// they are requesting the forums, let's start the build process PLEASE.
			
			// calling the forum class :D
			require_once("forum.class.php");
			$Forum = new Forum();
			$Forum->Init();
		}
		else if(substr($this->Page,0,5) == "login") //copypasta
		{
			// they are requesting the login, let's start the build process PLEASE.
			
			// calling the login class :D
			require_once("secure.class.php");
			$Secure = new Secure("login");
			$Secure->Init();
		}
		else if(substr($this->Page,0,6) == "logout") //copypasta
		{
			// they are requesting the login, let's start the build process PLEASE.
			
			// calling the login class :D
			require_once("secure.class.php");
			$Secure = new Secure("logout");
			$Secure->Init();
		}
		else if(substr($this->Page,0,7) == "overlay")
		{
			// calling the ads class :D
			require_once("overlay.class.php");
			$Overlay = new Overlay();
			$Overlay->Init();
		}
		else
		{
			// for everything else, there is just AnimeFTW.tv mobile :p
			
			// calling the content class :D
			require_once("content.class.php");
			$Content = new Content($this->Page);
			$Content->Init();
		}
	}
}

?>