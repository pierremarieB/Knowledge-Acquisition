<?php

class CourseModuleParser {

	protected $jsonContent;

	public function __construct($courseName,$jsonContent)
	{
		$this->jsonContent = $jsonContent;
		$file = fopen("./source/data-files/parsed-courses/{$courseName}","w");
		fwrite($file, json_encode($this->makeNgrams()));
	}

	public function makeNgrams()
	{
		$improvementsUnigrams = $strongPointsUnigrams = $q1Unigrams = $satisfiedOrNot = array();
		$improvementsBigrams = $strongPointsBigrams = $q1Bigrams = array();
		$nbQ2Unigrams = 0;
		$nbQ1Unigrams = 0;
		$nbQ1Bigrams = 0;
		$nbQ2Bigrams = 0;
		foreach($this->jsonContent as $question)
		{
			if($question->getQtype() === '2')
			{
				$improvementsQuestions = $question->getImprovements();
				$strongPointsQuestions = $question->getStrongPoints();
				self::parseAnswersForUnigrams($improvementsUnigrams,$improvementsQuestions,$nbQ2Unigrams);
				self::parseAnswersForUnigrams($strongPointsUnigrams,$strongPointsQuestions,$nbQ2Unigrams);			
				self::parseAnswersForBigrams($improvementsBigrams,$improvementsQuestions,$nbQ2Bigrams);			
				self::parseAnswersForBigrams($strongPointsBigrams,$strongPointsQuestions,$nbQ2Bigrams);			
			}
			else //then the question is of type "1", which means most of the time the answer will be "Yes" or "No", still some questions are open, therefore we need to parse tokens
			{	 //there aswell. Important to note that it's a tricky contest, we don't know if the words are in a "improvements needed" or "strong points" contest
				$answers = $question->getReponses(); 
				self::satisfiedOrNot($satisfiedOrNot,$question->getQuestion(),$answers);
				self::parseAnswersForUnigrams($q1Unigrams,$answers,$nbQ1Unigrams);
				self::parseAnswersForBigrams($q1Bigrams,$answers,$nbQ1Bigrams);
			}
		}
		$unigrams = array("strongPoints"=>self::cleanArray($strongPointsUnigrams),"improvements"=>self::cleanArray($improvementsUnigrams),"satisfied"=>$satisfiedOrNot,"q1"=>self::cleanArray($q1Unigrams),"count"=>$nbQ2Unigrams);
		$bigrams = array("strongPoints"=>self::cleanArray($strongPointsBigrams),"improvements"=>self::cleanArray($improvementsBigrams),"q1"=>self::cleanArray($q1Bigrams),"count"=>$nbQ2Bigrams);
		return array("unigrams"=>$unigrams,"bigrams"=>$bigrams);
	}

	public static function parseAnswersForUnigrams(&$array,$answers,&$nbUnigrams)
	{
		foreach($answers as $answer)
		{
			$tokens = preg_split('/\s+/', $answer);
			foreach($tokens as $token)
			{
				$cleanToken = preg_replace('/[^A-zÀ-ú\d\']+/i', '', $token);
				$nbUnigrams++;
				if(strlen($cleanToken) > 4)
					self::addFrequency($array,$cleanToken);
			}
		}
	}

	public static function parseAnswersForBigrams(&$array,$answers,&$nbBigrams)
	{
		foreach($answers as $answer)
		{
			$tokens = preg_split('/\s+/', $answer);
			for($i=0;$i < count($tokens)-1;$i++)
			{
				$token1 = preg_replace('/[^A-zÀ-ú\d\']+/i', '', $tokens[$i]);
				$token2 = preg_replace('/[^A-zÀ-ú\d\']+/i', '', $tokens[$i+1]);
				$bigram = $token1 . " " . $token2;
				$nbBigrams++;
				if(strlen($bigram) > 4)
					self::addFrequency($array,$bigram);
			}
		}
	}

	public static function addFrequency(&$array,$token)
	{
		if(array_key_exists($token, $array))
			$array[$token] += 1;
		else
			$array[$token] = 1;
	}

	public static function cleanArray($array) //delete from array all n-grams who occurs only one time, we're not interested in those
	{
		$trashTokens = array("les","c'est","des","la","le","de la","à la","qu'il","sur le","leurs","faire","avoir","qu'on","et la","et le");
		$cleanArray = $array;
		foreach($cleanArray as $token => $frequency)
		{
			if($frequency === 1 || in_array($token, $trashTokens))
				unset($cleanArray[$token]);
		}
		asort($cleanArray,true);
		return $cleanArray;
	}

	public static function satisfiedOrNot(&$array,$question,$answers)
	{
		$array[$question]["Oui"] = 0;
		$array[$question]["Non"] = 0;
		foreach($answers as $answer)
		{
			if(preg_match("/Oui/",$answer))
				$array[$question]["Oui"] += 1;
			elseif (preg_match("/Non/",$answer)) 
				$array[$question]["Non"] += 1;
		}
	}

}