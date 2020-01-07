
# OfficeExporter

This library will generate document file such as MsWord and OpenDoc

### Installation

To install library, simply:

    $ composer require aman.maurya/office-exporter @dev

### Requirements

php: >=5.6.40

### Getting Started

- First you have to create template in word processor (Microsoft Office 2007 and above) with extension [.docx].
- Then extract the main content file form word processor template. This can be done using php script or manually, [here](https://github.com/aman-maurya/OfficeExporter#step-to-extract-main-content-file-from-word-processor-template) in this example I have chosen manual process.
- Convert the XML file to [XSLT stylesheet](https://www.w3schools.com/xml/xsl_intro.asp). 

```xml  
<xsl:stylesheet version="1.0"  
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/">
		....
	</xsl:template>
</xsl:stylesheet>
```
- Generate XML form database result.

###### Basic

```php
require __DIR__ . 'vendor/autoload.php';

use OfficeExporter\Xml;

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

<!-- Output -->

<?xml version="1.0" encoding="UTF-8"?>

<employee>
	<row>
		<name>Vivian Warner</name>
		<empId>123</empId>
	</row>
	<row>
		<name>Shane Lloyd</name>
		<empId>456</empId>
	</row>
</employee>
```
###### Advance

```php
require __DIR__ . 'vendor/autoload.php';

use OfficeExporter\Xml;

$xml = new Xml('employee');

$xml->setData('row.name:vivian|age:13|address:123 street', [
	'name' => 'Vivian Warner',
	'empId.department:Engg' => '123',
]);

$xml->setData('row.name:vivian|age:30|address:456 street', [
	'names' => [
		['name' => 'person 1'],
		['phones' => [
			['phone' => '9023XXXXXXXX'],
			['phone' => '9223XXXXXXXX']
		]],
	],
	'empId.department:IT' => '456',
]);

$xml->generateXml();

<!-- Output -->

<?xml version="1.0" encoding="UTF-8"?>

<employee>
    <row name="vivian" age="13" address="123 street">
        <name>Vivian Warner</name>
        <empId department="Engg">123</empId>
    </row>
    <row name="vivian" age="30" address="456 street">
        <names>
            <name>person 1</name>
        </names>
        <names>
            <phones>
                <phone>9023XXXXXXXX</phone>
            </phones>
            <phones>
                <phone>9223XXXXXXXX</phone>
            </phones>
        </names>
        <empId department="IT">456</empId>
    </row>
</employee>


```
- Merge the XmL and XSLT to generate a new ZIP archive. 

```php
$download->setDocTemplate('sample_word.docx');
$download->setXsltSource('document.xslt');
```
### Step to extract main content file from word processor template

- Create the word file with template.
- Change that word file from .docx to .zip, and then extract that zip file.
- Go inside the extracted folder and find the folder with name word.
- Enter inside that folder and copy document.xml file and paste it at any location outside the extract folder and delete all extracted files.
- Now convert document.xml file to XSLT stylesheet
- Change it back word.zip file to word.docx file.

<p align="center">
  <img alt="Office Exporter in action" src="https://github.com/aman-maurya/OfficeExporter/blob/master/OfficeExporter.gif">
</p>

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



