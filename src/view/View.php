<?php

require_once("Router.php");
require_once("view/QuestionChart.php");

class View
{

	protected $router;
	protected $title;
	protected $content;

	public function __construct(Router $router,$directory)
	{
		$this->router = $router;
		$this->title = null;
		$this->content = null;
		$this->directory = $directory;
	}

	public function makeHomePage($directory)
	{
		$this->title = null;
	}


	public function makeCoursePage($course,$directory)
	{
		//var_dump($course->getContent());

		$this->title = "Analyse de la fillière ".self::cleanName($course->getCourseName());
		$ngrams = $course->getNgrams();
		$unigrams = $ngrams["unigrams"];
		$bigrams = $ngrams["bigrams"];

		$nbUnigrams = $unigrams["count"];
		$nbBigrams = $bigrams["count"];

		$strongPoints = array_merge($unigrams["strongPoints"],$bigrams["strongPoints"]);
		$improvements = array_merge($unigrams["improvements"],$bigrams["improvements"]);

		$courseQuestions = $course->getQuestionWithIndice();

		$mergeWithIndice = array(); //we don't use array_merge because we want to keep the information of the type of the ngram (strong point or improvement)
		foreach($strongPoints as $key => $value)
			$mergeWithIndice[$key] = array($value,0); //0 -> strongPoints
		foreach($improvements as $key => $value) 
			$mergeWithIndice[$key] = array($value,1); //1 -> improvements

		//self::sort_array_of_array($mergeWithIndice);
		//var_dump($mergeWithIndice);

		$this->content = "<div class='cat worldcloud'>";
		$this->content .= "<p>";
		//var_dump($mergeWithIndice);
		foreach($mergeWithIndice as $key => $array) {
			$color = $array[1] === 0 ? "green" : "red";
			$fontsize = $array[0]*5;
			if(count($tokens = preg_split('/\s+/', $key)) === 2){
				$freq = "Itérations : ".$array[0].", Fréquence : ".strval(($array[0]/$nbBigrams)*100)."%";
				$this->content .= "<span class='ngrams' style='color:".$color."; font-style: italic; font-size:".$fontsize."px;'><abbr title='".$freq."'>".$key." </abbr></span>";
			}
			else{
				$freq = "Itérations : ".$array[0].", Fréquence : ".strval(($array[0]/$nbUnigrams)*100)."%";
				$this->content .= "<span class='ngrams' style='color:".$color."; font-size:".$fontsize."px;'><abbr title='".$freq."'>".$key." </abbr></span>";
			}
		}
		$this->content .= "</p></div>";
		$this->content .= "<div class='cat satisfied'>";
		$this->content .= self::makeSatisfiedQuestions($ngrams["unigrams"]["satisfied"]);
		$this->content .= "</div>";

		$this->content .= "<div class='cat questions'>";

		//var_dump($courseQuestions);
		foreach($courseQuestions as $key=>$array) {
			$imgPath = '';

			if($array['sentiment'] === 0) {
				$imgPath = 'images/poucerouge.png';
			}
			else if($array['sentiment'] === 1) {
				$imgPath = 'images/poucevert.png';
			}
			else {
				$imgPath = 'images/interrogation.png';	
			}
			
			$this->content .= "<div class='cat question'>";
			$this->content .= "<img id='sentiment' src='".$imgPath."' alt='sentiment'>";
			$this->content .= "<p><strong>".ucfirst($key)."</strong></p><br><br>";
			$this->content .= "<p><u>Résumé:</u> ".$array['resume']."</p>";
			$this->content .= "</div>";
		}
		$this->content .= "</div>";

	}


	public static function makeSatisfiedQuestions($questions)
	{
		$content = "";
		$id = 0;
		foreach($questions as $question => $answer)
		{
			if(array_sum($answer)) //some qtype 1 questions are open, i.e not answered by yes or no, we're not interested about them here
			//array_sum($answer) === 0 if not a single "No" or "Yes" has been registered
			{
				/*$content .= "<p>".$question."</p>";
				$content .= "<p>Oui : ".$answer["Oui"]."</p>";
				$content .= "<p>Non : ".$answer["Non"]."</p>";*/
				$chart = new QuestionChart($question,$answer,$id);
				$content .= $chart->getChart();
				$id++;
			}
		}
		return $content;
	}

	public static function makeForm($directory)
	{
		$allFiles = $directory->getAllFiles();
		$content = "<form onchange='submit()' method='GET'><div class='form-wrapper'><select name='c'>";
		foreach ($allFiles as $file) {
			$cleanString = self::cleanName($file);
			$content .= '<option value="'.$file.'">'.$cleanString."</option>";
		}
		$content .= "</select><input type='submit' value='Analyser'></input></div></form>";
		return $content;
	}

	//sorts the array based on the number of iteration (we have an array of array with nb of iterations and type of ngram, improvement or strong point)
	public static function sort_array_of_array(&$array)
	{
	    $sortarray = array();
	    foreach ($array as $key => $row)
	    {
	        $sortarray[$key] = $row[0];
	    }

	    array_multisort($sortarray, SORT_ASC, $array);
	}


	public static function cleanName($name)
	{
		return preg_replace('/_.*/s', '',preg_replace('/\d{3}_Evaluation de la formation /', '',$name));
	}

	public function render()
	{
		$title = $this->title;
		$form = self::makeForm($this->directory);
		$content = $this->content;
		include("template.php");
	}


}

?>
