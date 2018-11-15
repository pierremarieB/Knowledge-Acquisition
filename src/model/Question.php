<?php

class Question {

	public $json;
	protected $question;
	protected $qtype;
	protected $improvements;
	protected $strongPoints;
	protected $formation;

	public function __construct($fileLine)
	{
		$this->json = json_decode($fileLine,true);
	}

	public function getQuestion(){
		return $this->json["question"];
	}

	public function getQtype(){
		return $this->json["qtype"];
	}

	public function getReponses()
	{
		return $this->json["réponses"];
	}

	public function getImprovements(){
		return $this->json["réponses"]["améliorations"];
	}

	public function getStrongPoints(){
		return $this->json["réponses"]["points-forts"];
	}

	public function getFormation(){
		return $this->json["formation"];
	}

	public function getJson(){
		return $this->json;
	}

}