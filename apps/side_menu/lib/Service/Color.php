<?php

namespace OCA\SideMenu\Service;

use OCA\Theming\ThemingDefaults;

/**
 * class Color.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class Color
{
    public function __construct(protected ThemingDefaults $theming)
    {
    }

    /**
     * @thanks https://stackoverflow.com/posts/54393956/revision
     */
    public function adjustBrightness(string $hexCode, float $adjustPercent): string
    {
        $hexCode = ltrim($hexCode, '#');

        if (3 == strlen($hexCode)) {
            $hexCode = $hexCode[0].$hexCode[0].$hexCode[1].$hexCode[1].$hexCode[2].$hexCode[2];
        }

        $hexCode = array_map('hexdec', str_split($hexCode, 2));

        foreach ($hexCode as &$color) {
            $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
            $adjustAmount = ceil($adjustableLimit * $adjustPercent);

            $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
        }

        return '#'.implode($hexCode);
    }

    public function getPrimaryColor()
    {
        return $this->theming->getColorPrimary();
    }

    public function getLightenPrimaryColor()
    {
        return $this->adjustBrightness($this->getPrimaryColor(), 0.2);
    }

    public function getDarkenPrimaryColor()
    {
        return $this->adjustBrightness($this->getPrimaryColor(), -0.2);
    }

    public function getDarkenPrimaryColor2()
    {
        return $this->adjustBrightness($this->getPrimaryColor(), -0.3);
    }

    public function getTextColorPrimary()
    {
        return $this->theming->getTextColorPrimary();
    }
}
