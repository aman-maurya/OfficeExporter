<?php

require __DIR__ . 'vendor/autoload.php';

use OfficeExporter\Xml;
use OfficeExporter\MsWord;

$xml = new Xml('employee');

$xml->setData('row', [
	'name' => 'Vivian Warner',
	'empId' => '123',
]);

$xml->setData('row', [
	'name' => 'Shane Lloyd',
	'empId' => '456',
]);

$xml->genrateXml();

$xmlString = $xml->__toString();

$download = new MsWord($xmlString);
$download->setDocTemplate('sample_word.docx');
$download->setXsltSource('document.xslt');
$download->create('sample.docx');
