window.onload = loaded;


function loaded(){
	hideW = document.getElementById("hide-worldcloud");
	hideC = document.getElementById("hide-charts");
	hideQ = document.getElementById("hide-questions");

	wc = document.getElementsByClassName("worldcloud")[0];
	charts = document.getElementsByClassName("satisfied")[0];
	questions = document.getElementsByClassName("questions")[0];

	wcOn = true;
	chartsOn = true;
	questionsOn = true;

	hideW.onclick = hideWclicked;
	hideC.onclick = chartsClicked;
	hideQ.onclick = questionsClicked;
}

function hideWclicked(){
	if(wcOn){
		wc.style.display = "none";
		hideW.style.borderLeft = "5px solid green";
		wcOn = false;
	}
	else
	{
		wc.style.display = "inline-block";
		hideW.style.borderLeft = "5px solid #bd4147";
		wcOn = true;
	}
}

function chartsClicked(){
	if(chartsOn){
		charts.style.display = "none";
		hideC.style.borderLeft = "5px solid green";
		chartsOn = false;
	}
	else
	{
		charts.style.display = "inline-block";
		hideC.style.borderLeft = "5px solid #bd4147";
		chartsOn = true;
	}
}

function questionsClicked() {
	if(questionsOn){
		questions.style.display = "none";
		hideQ.style.borderLeft = "5px solid green";
		questionsOn = false;
	}
	else
	{
		questions.style.display = "inline-block";
		hideQ.style.borderLeft = "5px solid #bd4147";
		questionsOn = true;
	}
}