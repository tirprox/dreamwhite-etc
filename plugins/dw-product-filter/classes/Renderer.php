<?php
namespace Dreamwhite\Plugins\ProductFilter;
class Renderer
{



    public static function tag($content)
    {
        echo "<div style='font-size: 12px; display: inline-block; padding: 0px 4px; margin: 2px;border: 1px solid #cccccc'>$content</div>";
    }

    public static function color($content)
    {
        echo "<div style='font-size: 12px; display: inline-block; padding: 0px 4px; margin: 2px;border: 1px solid #cccccc'>$content</div>";
    }


    public static function header($content)
    {
        echo "<div class='dw-filter-attribute-header' style=''><span class='title'>$content</span><span class='collapse-icon'></span></div>";
    }

    public static function p($content)
    {
        echo "<p style='font-size: 12px; margin-bottom: 4px'>$content</p>";
    }

    public static function a($content, $href)
    {
        echo "<a style='font-size: 12px; margin-bottom: 0px; display: block;' href='$href'>$content</a>";
    }




    public static function attribute($header, $attr, $data, $class) {
            $headerRendered = false;

            foreach ($data as $item) {
                if (!$headerRendered && $item !== '') {
                    echo "<div class='dw-filter-attr-block'>";
                    self::header($header);
                    $headerRendered = true;
                }
                if ($item !== '') {
                    echo '<a ' . $class($attr, $item) . self::data($attr, $item) . '><span class="dw-filter-button-text">' . $item . ' </span></a>';
                }
            }
            if ($headerRendered) echo "</div>";

    }

    private static function data($attr, $value) {
            return 'data-attr-type="' . $attr . '" data-attr-value="' . $value . '"';
    }

}