<!DOCTYPE html>
<html lang="fr">
<head>
	<title><?php echo $title ?></title>
	<meta charset="UTF-8" />
	<link href="src/view/style.css" type="text/css" rel="stylesheet">
	<script src="src/view/script.js"></script>
</head>
<body>
	<header>
		<h1>Analyse de retour d'information</h1>
		<div id="choix-filliere"><?php echo $form;?></div>
		<div id="hide-worldcloud" class="hide">Cacher le worldcloud</div>
		<div id="hide-charts" class="hide">Cacher les graphiques</div>
		<div id="hide-questions" class="hide">Cacher le détail des questions</div>
	</header>
	<main>
		<h2><?php echo $title; ?></h2>
		<?php echo $content; ?>
	</main>
	<footer>
		<p>développé par <span class="author">Pierre-Marie Brieda</span> et <span class="author">Alexandre Gravouille</span></p>
	</footer>
</body>
</html>

