# OfficeExporter

This library will generate document file such as MsWord and OpenDoc

### Installation

To install PHP Curl Class, simply:

    $ composer require aman.maurya/office-exporter @dev

### Requirements

php: >=5.6.40

### Quick Start and Examples

```php
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

$xml->generateXml();

$xmlString = $xml->__toString();

$download = new MsWord($xmlString);
$download->setDocTemplate('sample_word.docx');
$download->setXsltSource('document.xslt');
$download->create('sample.docx');

```



