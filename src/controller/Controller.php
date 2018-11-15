<?php 

class Controller 
{

	private $directory;
	private $view;

	public function __construct($directory,$view)
	{
		$this->directory = $directory;
		$this->view = $view;
	}

	public function analyzeCourse($courseName)
	{
		$course = new CourseModule($courseName);
		$this->view->makeCoursePage($course,$this->directory);
	}
}