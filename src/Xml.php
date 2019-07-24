<?php

namespace OfficeExporter;

/**
 * Description of Xml
 *
 * @author aman.maurya
 */
class Xml {

    /**
     * @var string Filename/path to be used when XML is output as a file
     */
    protected $filename;

    /**
     * @var string MIME type for when XML is downloaded
     */
    protected $fileType = 'text/xml';

    /*     * #@+
     * @var bool Indicates if the XML is to be saved to local or download file
     */
    protected $local = false;
    protected $download = false;
    /*     * #@- */

    /**
     * @var string Name of XML root element (default: root)
     */
    protected $rootname = 'root';

    /**
     * @var string Name of XML child elements (default: row)
     */
    protected $rowname = 'row';

    /**
     * @var string The XML generated from the database result
     */
    protected $xml = '';
    private $w = null;

    public function __construct($rootname = null) {
        $this->w = new \XmlWriter();
        $this->w->openMemory();
        $this->w->setIndent(true);
        $this->w->setIndentString("    ");
        $this->w->startDocument('1.0', 'utf-8');
        if (!empty($rootname))
            $this->setRootName($rootname);
        $this->startElement($this->rootname);
    }

    /**
     *
     * @return void
     */
    function generateXml() {
        $this->w->endElement();
        $this->w->endDocument();
        $this->xml = $this->w->outputMemory();
        unset($this->w);
    }

    /**
     *
     * @return void
     */
    function setData($rowName = null, $data) {

        if (!empty($rowName))
            $this->startElement($rowName);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (array_keys($value) === range(0, count($value) - 1)) {
                    foreach ($value as $childKey => $childValues) {
                        $this->startElement($key);
                        foreach ($childValues as $k => $val) {
                            $this->setData(null, [$k => $val]);
                        }
                        $this->endElement();
                    }
                } else {
                    $this->startElement($key);
                    foreach ($value as $childKey => $childValues) {
                        if (is_array($childValues)) {
                            $this->startElement($childKey);
                            foreach ($childValues as $k => $val) {
                                $this->setData(null, [$k => $val]);
                            }
                            $this->endElement();
                        } else {
                            $this->setData(null, [$childKey => $childValues]);
                        }
                    }
                    $this->endElement();
                }
            } else {
                $this->startElement($key);
                $this->writeText($value);
                $this->endElement();
            }
        }

        if (!empty($rowName))
            $this->endElement();
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->xml;
    }

    /**
     *
     * @return void
     */
    private function setRootName($name) {
        $this->rootname = $name;
    }

    /**
     * Removes HTML tags and splits the remaining content on newline characters
     *
     * Consecutive newline characters are treated as a single character. If no newline
     * characters are detected, the cleaned up text is returned as a single array element.
     *
     * @param string $value Text to be processed
     * @return array One or more text elements with HTML tags and newline characters removed
     */
    protected function stripHtml($value) {
        $value = strip_tags($value);
        $paras = preg_split('/[\r\n]+/', $value);
        return $paras;
    }

    /**
     * Tests whether the supplied value is a valid XML identifier
     *
     * @param string $name Value to be checked
     * @throws \Exception
     */
    protected function isValidName($name) {
        if (!preg_match('/^(?!xml|\d|-|\.)[-\w.]+$/', $name)) {
            throw new \Exception("$name is not a valid XML identifier.");
        }
    }

    /**
     * Generates the HTTP headers for the download file using the $fileType
     * and $filename properties to insert the appropriate values in the
     * Content-Type and Content-Disposition headers.
     */
    protected function outputHeaders() {
        header('Content-Type: ' . $this->fileType);
        header('Content-Disposition: attachment; filename=' . $this->filename);
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    /**
     *
     * @return void
     */
    function download() {
        $this->outputHeaders();
        file_put_contents('php://output', $this->xml);
        exit;
    }

    /**
     *
     * @return void
     */
    function save($path) {
        $this->outputHeaders();
        if (is_writable($path))
            file_put_contents($path, $this->xml);
        else
            throw new Exception('Directory is not writable.');
        exit;
    }

    /**
     *
     * @return void
     */
    function startElement($key) {
        if (strpos($key, '.') !== FALSE) {
            $data = $this->getNodeData($key);
            $this->w->startElement($data['key']);
            foreach ($data['attribute'] as $value) {
                $this->w->writeAttribute($value['k'], $value['v']);
            }
        } else {
            $this->isValidName($key);
            $this->w->startElement($key);
        }
    }

    /**
     *
     * @return void
     */
    function endElement() {
        $this->w->endElement();
    }

    /**
     *
     * @return void
     */
    function writeText($content) {
        $this->w->text($content);
    }

    /**
     *
     * @return array
     */
    function getNodeData($key) {
        $data = [];
        $nodeData = explode('.', $key);
        if (count($nodeData) !== 2)
            throw new \Exception('Invalid key attribute syntax.');

        $this->isValidName($nodeData[0]);
        $data['key'] = $nodeData[0];

        $attributes = explode('|', $nodeData[1]);

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $attr = explode(':', $attribute);
                if (isset($attr[0])) {
                    $this->isValidName($attr[0]);
                    $data['attribute'][] = [
                        'k' => $attr[0],
                        'v' => isset($attr[1]) ? $attr[1] : '',
                    ];
                }
            }
        }

        return $data;
    }

}
