
<?php

//scans all the courses that we can analyze
class CourseModuleDirectory {

	private $allFiles;

	public function __construct($directory){
		$this->allFiles = array_diff(scandir($directory), array('..', '.','parsed-courses'));
		
		//fopen doesn't work on files with accents, says the file doesn't exist even if it does
		//comment the following lines if already done once, useless to do it more than once
		/*foreach($this->allFiles as $file)
		{
			$newName = preg_replace("/é|è/", "e", $file);
			$newName = preg_replace("/ô/", "o", $newName);
			rename("./source/data-files/{$file}","./source/data-files/{$newName}");
		}*/
	}

	public function getAllFiles(){
		return $this->allFiles;
	}
}