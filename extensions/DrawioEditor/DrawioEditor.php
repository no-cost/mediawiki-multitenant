<?php

class DrawioEditor {
    public static function onParserSetup(&$parser) {
        $parser->setFunctionHook('drawio', 'DrawioEditor::parse');
    }

    public static function onOutputPageParserOutput(&$outputPage, $parseroutput) {
        $outputPage->addModules('ext.drawioeditor');
    }

    public static function parse(&$parser, $name = null) {
        global $wgUser, $wgEnableUploads;
        global $wgDrawioEditorImageType;
        global $wgDrawioEditorImageInteractive;

        $parser->disableCache();

        $opts = array();
        foreach (array_slice(func_get_args(), 2) as $rawopt) {
            $opt = explode('=', $rawopt, 2);
            $opts[trim($opt[0])] = count($opt) === 2 ? trim($opt[1]) : true;
        }

        $opt_type = array_key_exists('type', $opts) ? $opts['type'] : $wgDrawioEditorImageType;
        $opt_interactive = array_key_exists('interactive', $opts) ? true : $wgDrawioEditorImageInteractive;
        $opt_height = array_key_exists('height', $opts) ? $opts['height'] : 'auto';
        $opt_width = array_key_exists('width', $opts) ? $opts['width'] : '100%';
        $opt_max_width = array_key_exists('max-width', $opts) ? $opts['max-width'] : false;

        if ($name == null || !strlen($name))
            return self::errorMessage('Usage Error');
        if (!in_array($opt_type, ['svg', 'png']))
            return self::errorMessage('Invalid type');

        $len_regex = '/^((0|auto|chart)|[0-9]+(\.[0-9]+)?(px|%|mm|cm|in|em|ex|pt|pc))$/';
        $len_regex_max = '/^((0|none|chart)|[0-9]+(\.[0-9]+)?(px|%|mm|cm|in|em|ex|pt|pc))$/';

        if (!preg_match($len_regex, $opt_height))
            return self::errorMessage('Invalid height');
        if (!preg_match($len_regex, $opt_width))
            return self::errorMessage('Invalid width');

        if ($opt_max_width) {
            if (!preg_match('/%$/', $opt_width))
                return self::errorMessage('max-width is only allowed when width is relative');
            if (!preg_match($len_regex_max, $opt_max_width))
                return self::errorMessage('Invalid max-width');
        } else {
            $opt_max_width = 'chart';
        }

        $name = wfStripIllegalFilenameChars($name);
        $dispname = htmlspecialchars($name, ENT_QUOTES);

        $id = mt_rand();

        $img_name = $name . ".drawio." . $opt_type;
        $img = wfFindFile($img_name);
        if ($img) {
            $img_url = $img->getViewUrl();
            $img_url_ts = $img_url . '?ts=' . $img->nextHistoryLine()->img_timestamp;
            $img_desc_url = $img->getDescriptionUrl();
            $img_height = $img->getHeight() . 'px';
            $img_width = $img->getWidth() . 'px';
        } else {
            $img_url = '';
            $img_url_ts = '';
            $img_desc_url = '';
            $img_height = 0;
            $img_width = 0;
        }

        $css_img_height = $opt_height === 'chart' ? $img_height : $opt_height;
        $css_img_width = $opt_width === 'chart' ? $img_width : $opt_width;
        $css_img_max_width = $opt_max_width === 'chart' ? $img_width : $opt_max_width;

        $readonly = (!$wgEnableUploads
            || (!$img && !$wgUser->isAllowed('upload'))
            || ($img && !$wgUser->isAllowed('reupload'))
            || $parser->getTitle()->isProtected('edit')
        );

        $edit_ahref = sprintf(
            "<a href='javascript:editDrawio(\"%s\", %s, \"%s\", %s, %s, %s, %s)'>",
            $id,
            json_encode($img_name, JSON_HEX_QUOT | JSON_HEX_APOS),
            $opt_type,
            $opt_interactive ? 'true' : 'false',
            $opt_height === 'chart' ? 'true' : 'false',
            $opt_width === 'chart' ? 'true' : 'false',
            $opt_max_width === 'chart' ? 'true' : 'false'
        );

        $output = '<div>';
        $output .= '<div id="drawio-img-box-' . $id . '">';

        if (!$readonly) {
            $output .= '<div align="right">';
            $output .= '<span class="mw-editsection">';
            $output .= '<span class="mw-editsection-bracket">[</span>';
            $output .= $edit_ahref;
            $output .= wfMessage('edit')->text() . '</a>';
            $output .= '<span class="mw-editsection-bracket">]</span>';
            $output .= '</span>';
            $output .= '</div>';
        }

        $img_style = sprintf(
            'height: %s; width: %s; max-width: %s;',
            $css_img_height,
            $css_img_width,
            $css_img_max_width
        );
        if (!$img) {
            $img_style .= ' display:none;';
        }

        if ($opt_interactive) {
            $img_fmt = '<object id="drawio-img-%s" data="%s" type="text/svg+xml" style="%s"></object>';
            $img_html = sprintf($img_fmt, $id, $img_url_ts, $img_style);
        } else {
            $img_fmt = '<img id="drawio-img-%s" src="%s" title="%s" alt="%s" style="%s"></img>';
            $img_html = '<a id="drawio-img-href-' . $id . '" href="' . $img_desc_url . '">';
            $img_html .= sprintf($img_fmt, $id, $img_url_ts, 'drawio: ' . $dispname, 'drawio: ' . $dispname, $img_style);
            $img_html .= '</a>';
        }

        if (!$img) {
            $output .= sprintf(
                '<div id="drawio-placeholder-%s" class="DrawioEditorInfoBox">' .
                '<b>%s</b><br/>empty draw.io chart</div> ',
                $id,
                $dispname
            );
        }
        $output .= $img_html;
        $output .= '</div>';

        $output .= '<div id="drawio-iframe-box-' . $id . '" style="display:none;">';
        $output .= '<div id="drawio-iframe-overlay-' . $id . '" class="DrawioEditorOverlay" style="display:none;"></div>';
        $output .= '</div>';

        $output .= '</div>';

        if ($img) {
            $parser->getOutput()->addImage($img->getTitle()->getDBkey());
        }

        return array($output, 'isHTML' => true, 'noparse' => true);
    }

    private static function errorMessage($msg) {
        $output = '<div class="DrawioEditorInfoBox" style="border-color:red;">';
        $output .= '<p style="color: red;">DrawioEditor Usage Error:<br/>' . htmlspecialchars($msg) . '</p>';
        $output .= '</div>';

        return array($output, 'isHTML' => true, 'noparse' => true);
    }
}
