<?php
$_MODULE = [
	"name" => "Block Question",
	"description" => "Combine blocks as question/answer/solution/theory sets",
	"namespace" => "\\modules\\block_question",
	"config_controller" => "administrator\\BlockQuestion",
	"controllers" => [
		"administrator\\BlockQuestion"
	],
	"default_config" => [
		"types" => [
			"normal" => [
			  "id" => 1,
			  "name" => "Normal"
			]
		]
	]
];
