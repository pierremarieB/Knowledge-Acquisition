<?php

//creates a pie chart of the number of 'yes' and 'no' answers of a question
class QuestionChart {

	protected $label = array();
	protected $data = array();
	protected $type;
	protected $id;

	public function __construct($question,$answers,$id){
		$this->type = "pie";
		$this->id = $id;
		$this->question = $question;
		$this->positive = $answers["Oui"];
		$this->negative = $answers["Non"];
	}

	public static function getCDN()
	{
		return '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js"></script>';
	}

	public function makeChart(){
		$chart = "<script>var ctx = document.getElementById('chart-".$this->id."').getContext('2d');";
		$chart .= "var chart = new Chart(ctx, {";
		$chart .= "type: 'pie',";
		$chart .= "options : {";
		$chart .= "title : { display: true,";
		$chart .= "text: '".preg_replace("/\?.*/", "",ucfirst($this->question))." ?'}},";
		$chart .= "data: {";
		$chart .= "labels :['Oui','Non'],";
		$chart .= "datasets: [{ label: 'Answer',";
		$chart .= "data :[".$this->positive.",".$this->negative."],";
		$chart .= "backgroundColor: ['rgb(124,252,0)','rgb(255,0,0)'] }]}});</script>";
		return $chart;
	}

	public function getChart()
	{
		$finalString = "";
		if($this->id === 0)
			$finalString .= self::getCDN();
		$finalString .= "<canvas id='chart-".$this->id."'></canvas>";
		$finalString .= $this->makeChart();
		return $finalString;
	}

}

?>