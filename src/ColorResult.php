<?php
namespace Img2Ascii;

class ColorResult extends Result {
    public $blockChar = '#'; // can also be an HTML entity, e.g. &#x2588;

    protected function transformValue($value) {
        $r = floor($value[0]);
        $g = floor($value[1]);
        $b = floor($value[2]);
        return "<span style='color:rgb({$r},{$g},{$b})'>{$this->blockChar}</span>";
    }
}
