<?php
/**
 * The basic Result class for the Img2Ascii processor.
 *
 * It defines common output functions for the implementing output
 * transformers.
 *
 * (c) 2016 Alex Schenkel
 */
namespace Img2Ascii;

class Result {
    public $ascii;
    public function __construct(&$ascii) {
        $this->ascii =& $ascii;
    }

    public function writeFile($path, $lineEnding = "\n") {
        $fh = fopen($path,'w');
        foreach($this->ascii as $line) {
            foreach($line as $value) {
                $value = $this->transformValue($value);
                fputs($fh, $value);
            }
            fputs($fh,$lineEnding);

        }
        fclose($fh);
        return $this;
    }
}
