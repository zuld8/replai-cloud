<?php

namespace App\Process\TemplateEditor;

class StyleManager
{
    function __construct()
    {
        if(config('editor.style_manager.limited_selectors', false)){
            $this->sectors = [];
        }
        
    }
}