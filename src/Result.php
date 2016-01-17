<?php
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
