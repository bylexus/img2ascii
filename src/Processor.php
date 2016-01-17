<?php
/**
 * The Processor class processes an image and prepares
 * the color values needed for the ascii output.
 *
 * Its main job is to process each pixel, do color calculations and
 * store an internal color array for later ascii output, which then is done
 * by its helper Result classes.
 *
 * (c) 2016 Alex Schenkel
 */
namespace Img2Ascii;

class Processor {
    protected $imgPath;
    protected $image;
    protected $imageWidth;
    protected $imageHeight;

    protected $result;

    public function __construct($pathOrImgres) {
        if (is_file($pathOrImgres)) {
            $this->imgPath = $pathOrImgres;
            $this->image = imagecreatefromstring(file_get_contents($pathOrImgres));
        } else {
            $this->image = $pathOrImgres;
        }
        try {
            $this->imageWidth = imagesx($this->image);
            $this->imageHeight = imagesy($this->image);
        } catch (\Exception $e) {
            throw new \Exception("Image creation failed. Is the given resource a valid image?");
        }
    }

    public function asciify($pixelWidth = 10) {
        $charsX = floor($this->imageWidth / $pixelWidth);
        return $this->asciifyToWidth($charsX);
    }

    public function asciifyToWidth($charsX) {
        $charsX = (int)$charsX;
        if (!$charsX) $charsX = floor($this->imageWidth / 10);
        $this->result = null;

        $blockWidth = $this->imageWidth / (float)$charsX;
        $blockHeight = $blockWidth * 8.0/5.0;
        $blockWidth = max(1,floor($blockWidth));
        $blockHeight = max(1,floor($blockHeight));

        $ascii = array();

        for ($y = 0; $y < $this->imageHeight; $y += $blockHeight) {
            for ($x = 0; $x < $this->imageWidth; $x += $blockWidth) {
                $value = [0,0,0];
                if ($x === 0) {
                    $ascii[$y / $blockHeight] = array();
                }
                $counter = 0;
                for ($innerY = $y; $innerY < $y + $blockHeight; $innerY++) {
                    if ($innerY > $this->imageHeight-1) break;
                    for ($innerX = $x; $innerX < $x + $blockWidth; $innerX++) {
                        if ($innerX > $this->imageWidth-1) break;
                        $counter++;
                        $rgb = imagecolorat($this->image,$innerX, $innerY);
                        $rgb = array($rgb >> 16, $rgb >> 8 & 255, $rgb & 255);
                        $value[0] += $rgb[0];
                        $value[1] += $rgb[1];
                        $value[2] += $rgb[2];
                    }
                }
                $value[0] = $value[0] / $counter;
                $value[1] = $value[1] / $counter;
                $value[2] = $value[2] / $counter;
                $ascii[$y / $blockHeight][$x / $blockWidth] = $value;
            }
        }
        $this->result = $ascii;
        return $this;
    }

    protected function saturate($ascii) {
        foreach($ascii as $y =>$line) {
            foreach($line as $x => $value) {
                $ascii[$y][$x] = $value[0]*0.299 + $value[1]*0.587 + $value[2]*0.114;
            }
        }
        return $ascii;
    }

    public function colorResult($blockChar = '#') {
        $res = new ColorResult($this->result);
        $res->blockChar = $blockChar;
        return $res;
    }

    public function result($symbols = "@%#*+=-:. ") {
        $res = new BlackWhiteResult($this->saturate($this->result));
        $res->symbols = $symbols;
        return $res;
    }
}
