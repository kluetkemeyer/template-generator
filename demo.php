<?php

require_once __DIR__ . '/libs/spyc/Spyc.php';
require_once __DIR__ . '/libs/lessphp/lessc.inc.php';
require_once __DIR__ . '/src/TemplateGenerator.php';

$layout = TemplateGenerator::createFromFile(__DIR__ . '/examples/darkblue.yml');
$layout->generate(__DIR__ . '/out');