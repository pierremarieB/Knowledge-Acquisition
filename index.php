<?php

set_include_path("./src");
require_once("model/CourseModule.php");
require_once("model/CourseModuleDirectory.php");
require_once("Router.php");

$test = new CourseModule("909_Evaluation de la formation L3 Chimie_LCCH15-211_filtre.json");
$test = new CourseModule("909_Evaluation de la formation L3 Mathematiques_LCMA15-211_filtre.json");


$directoryToScan = "./source/data-files/";
$router = new Router(new CourseModuleDirectory($directoryToScan));
$router->main();

