<?php 

require_once("view/View.php");
require_once("controller/Controller.php");

class Router {

	private $directory;

	public function __construct(CourseModuleDirectory $directory)
	{
		$this->directory = $directory;
	}

	public function main()
	{
		$view = new View($this,$this->directory);
		$ctl = new Controller($this->directory,$view);
		$courseToAnalyze = array_key_exists("c", $_GET) ? $_GET["c"] : null; 
		if($courseToAnalyze)
		{
			$ctl->analyzeCourse($courseToAnalyze);
			$view->render();
		}
		else
		{
			$view->makeHomePage($this->directory);
			$view->render();
		}
	}


}