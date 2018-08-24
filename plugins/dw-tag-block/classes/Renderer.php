<?php
namespace Dreamwhite\Plugins\TagBlock;

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
        echo "<h3 style=''>$content</h3>";
    }

    public static function p($content)
    {
        echo "<p style='font-size: 12px; margin-bottom: 4px'>$content</p>";
    }

    public static function a($content, $href)
    {
        echo "<a class='dw-tag-block-item' href='$href'>$content</a>";
    }

    public static function tag_block_parent($content, $href)
    {
        echo "<a class='dw-tag-block-parent-item' href='$href'>$content</a>";
    }

}