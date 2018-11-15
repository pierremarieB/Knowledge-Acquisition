<?php

require_once("model/Question.php");
require_once("model/CourseModuleParser.php");

class CourseModule {

	protected $courseName;
	protected $jsonContent; // array of all the json objects of the course
	protected $nbQuestions;
	protected $unigrams;
	protected $ngrams;
	protected $questionWithIndice;

	public function __construct($courseName)
	{
		$this->courseName = $courseName;
		$this->jsonContent = array();
		$handle = fopen("./source/data-files/{$courseName}", "r");
		if ($handle) 
		{
	    	while (($line = fgets($handle)) !== false)
	    	{
	    		array_push($this->jsonContent, new Question($line));
	    		$this->nbQuestions += 1;
	    	}
	    	fclose($handle);
		} 
		else
		    print_r("Error opening the file");

		//if the course hasn't been parsed before, we parse it (it's our cache)
		if(!file_exists("./source/data-files/parsed-courses/{$this->courseName}")) 
			$parser = new CourseModuleParser($this->courseName,$this->jsonContent);
		$this->ngrams = json_decode(file_get_contents("./source/data-files/parsed-courses/{$courseName}"),true);

		//var_dump($this->jsonContent);
		$this->questionWithIndice = array();

		foreach ($this->jsonContent as $key => $array) {
			$tempArray = array();
			$sentiment = 0;

			$questionAnswer = '';

			if($array->json['qtype'] === '2') {
				$tempUnigram = array();

				if(sizeof($array->json['réponses']['améliorations']) > sizeof($array->json['réponses']['points-forts'])) {
					$sentiment = 0;
				}
				if(sizeof($array->json['réponses']['améliorations']) < sizeof($array->json['réponses']['points-forts'])) {
					$sentiment = 1;
				}
				else if(sizeof($array->json['réponses']['améliorations']) === sizeof($array->json['réponses']['points-forts'])) {
					$sentiment = 1;
				}

				foreach ($array->json['réponses']['améliorations'] as $answer) {
					$tokens = preg_split('/\s+/', $answer);
					foreach($tokens as $token) {
						$cleanToken = preg_replace('/[^A-zÀ-ú\d\']+/i', '', $token);
						if(strlen($cleanToken) > 4)
							$this->addFrequency($tempUnigram,$cleanToken);
					}
				}

				foreach ($array->json['réponses']['points-forts'] as $answer) {
					$tokens = preg_split('/\s+/', $answer);
					foreach($tokens as $token) {
						$cleanToken = preg_replace('/[^A-zÀ-ú\d\']+/i', '', $token);
						if(strlen($cleanToken) > 4)
							$this->addFrequency($tempUnigram,$cleanToken);
					}
				}
			}
			else {
				//var_dump($array->json['réponses']);
				$tempUnigram = array();
				$compteurOui = 0;
				$compteurNon = 0;
				foreach ($array->json['réponses'] as $answer) {
					$tokens = preg_split('/\s+/', $answer);
					if(ucfirst($tokens[0]) === 'Oui') {
						$compteurOui++;
					}
					else if(ucfirst($tokens[0]) === 'Oui') {
						$compteurNon++;
					}
					foreach($tokens as $token) {
						$cleanToken = preg_replace('/[^A-zÀ-ú\d\']+/i', '', $token);
						if(strlen($cleanToken) > 4)
							$this->addFrequency($tempUnigram,$cleanToken);
					}
				}
				if($compteurOui > $compteurNon) {
					$sentiment = 1;
				}
				else if($compteurOui < $compteurNon) {
					$sentiment = 0;
				}
				else if($compteurOui === $compteurNon){
					$sentiment = 3;
				}
			}
			$tempArray = $tempUnigram;
			//var_dump($tempArray);
			$resume = $this->getResume($array,$tempArray);
			
			$this->questionWithIndice[$array->json['question']] = array(
				'qtype' => $array->json['qtype'],
				'sentiment' => $sentiment,
				'resume' => $resume,
				'unigram' => $tempArray);
			
		}
		//var_dump($this->questionWithIndice);
	}

	public function getContent()
	{
		return $this->jsonContent;
	}

	public function getCourseName()
	{
		return $this->courseName;
	}

	public function getNgrams()
	{
		return $this->ngrams;
	}


	public function getNbQuestion()
	{
		return $this->nbQuestions;
	}

	public function getQuestionWithIndice() 
	{
		return $this->questionWithIndice;
	}

	public static function addFrequency(&$array,$token)
	{
		if(array_key_exists($token, $array))
			$array[$token] += 1;
		else
			$array[$token] = 1;
	}

	public function getResume($array, $frequencies) {
		$totalText = '';
		$resume = '';

		arsort($frequencies);
		$frequencies = array_slice($frequencies, 0, 10);
		//var_dump($frequencies);
		//var_dump($array->json['réponses']);
		foreach ($array->json['réponses'] as $answers) {
			if(is_array($answers)) {
				foreach ($answers as $value) {
					$totalText .= $value.' ';
				}
			}
			else {
				$ouiNon = array(
					'Oui',
					'Non',
					'Oui,',
					'Non,');
				if(!in_array(explode(' ', $answers)[0],$ouiNon)) {
					$totalText .= $answers.' ';
				}
				$totalText .= '';
			}
		}
		//var_dump($frequencies);
		$ranking = array();
		foreach(preg_split('/(?<=[.?!\r\n\v{2}])\s+(?=[a-z])/i',$totalText) as $index=>$s) {
			$tokens = preg_split('/\s+/', $s);
			$score = 0;
			//var_dump($tokens);
			foreach($tokens as $token) {
				$cleanToken = preg_replace('/[^A-zÀ-ú\d\']+/i', '', $token);

				if(strlen($cleanToken) > 4)
					if(array_key_exists($cleanToken, $frequencies)) {
						//var_dump($cleanToken);
						$score += $frequencies[$cleanToken];
					}
			}
			$ranking[$s] = $score;
		}
		arsort($ranking);
		//var_dump($ranking);

		if(isset(array_keys($ranking)[0])) {
			$resume .= array_keys($ranking)[0]; 	
		}
		else {
			$resume = 'Summary not available.';
		}
		if(isset(array_keys($ranking)[1])) {
			$resume .= array_keys($ranking)[1]; 
		}
		else {
			$resume = 'Résumé indisponible: pas assez de données.';
		}
		//var_dump($resume);
		return $resume;
	}

}