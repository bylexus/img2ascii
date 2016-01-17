<?php
/**
 * The black / white or "ascii art" output processor.
 * Transforms the input color values into ascii art characters.
 *
 * (c) 2016 Alex Schenkel
 */
namespace Img2Ascii;

class BlackWhiteResult extends Result {
    public $symbols = "@%#*+=-:. ";

    protected function gray2ascii($gray) {
        $level = round($gray / 255.0 * (strlen($this->symbols)-1));
        return $this->symbols[(int)min($level,strlen($this->symbols)-1)];
    }
    protected function transformValue($value) {
        return $this->gray2ascii($value);
    }
}
