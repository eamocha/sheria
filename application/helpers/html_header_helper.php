<?php

function print_css($css)
{
    $ci = &get_instance();
    foreach ($css['files'] as $cssFile) {
        $extra = '';
        if (isset($cssFile['extra'])) {
            foreach ($cssFile['extra'] as $extraK => $extraV) {
                $extra .= ' ' . $extraK . '="' . $extraV . '"';
            }
        }
        echo $cssFile['remote'] ?
                '<link href="' . $cssFile['src'] . '" rel="stylesheet" type="text/css"' . $extra . ' />' :
                '<link href="' . BASEURL . $css['base'] . $cssFile['src'] . '?r=' . $ci->productVersion. '.o092" rel="stylesheet" type="text/css"' . $extra . ' />';
        echo PHP_EOL;
    }
    echo '<!--[if gte IE 9]>
<style type="text/css">
.gradient {filter: none;}
</style>
<![endif]-->
<!--[if lt IE 9]>
<style>
.navbar-collapse.collapse {height: auto; overflow: visible;}
.form-control{min-height:20px;}
</style>
<![endif]-->
<!--[if !IE 7]>
<style type="text/css">
/*#wrap {display:table;height:100%}*/
</style>
<![endif]-->
';
}

function print_js($js)
{
    $ci = &get_instance();
    $jsData = '';
    foreach ($js['files'] as $jsFile) {
        $extra = '';
        if (isset($jsFile['extra'])) {
            foreach ($jsFile['extra'] as $extraK => $extraV) {
                $extra .= ' ' . $extraK . '="' . $extraV . '"';
            }
        }
        $src = $jsFile['remote'] ? $jsFile['src'] : BASEURL . $js['base'] . $jsFile['src'] . '?v=' . $ci->productVersion . '.o092';
        $jsData .= '<script type=\'text/javascript\' src=\'' . $src . '\' ' . $extra . '><\/script>';
        echo PHP_EOL;
    }
    echo '<script type="text/javascript">document.write("' . $jsData . '"); </script>';
}

function app_url($href, $module = '')
{
    return rtrim(BASEURL . (!empty($module) ? 'modules/' . $module . '/' : '') . $href, '/') . '/';
}

/**
 * Drop-down Menu
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	string
 * @param	string
 * @return	string
 */
if (!function_exists('form_dropdown_extended')) {
    function form_dropdown_extended($name = '', $options = array(), $selected = array(), $selectAttr = '', $optAttr = array('key' => 'id', 'val' => 'name'), $firstLine = '')
    {
        if (!is_array($selected)) {
            $selected = array($selected);
        }

        // If no selected state was submitted we will attempt to set it automatically
        if (count($selected) === 0) {
            // If the form name appears in the $_POST array we have a winner!
            if (isset($_POST[$name])) {
                $selected = array($_POST[$name]);
            }
        }

        if ($selectAttr != '') {
            $selectAttr = ' ' . $selectAttr;
        }

        $multiple = (count($selected) > 1 && strpos($selectAttr, 'multiple') === false) ? ' multiple="multiple"' : '';

        $form = '<select name="' . $name . '"' . $selectAttr . $multiple . ">\n";
        if (!empty($firstLine)) {
            $form .= '<option value="">' . $firstLine . '</option>';
        }
        if (!is_array($optAttr)) {
            $optAttr = array('key' => 'id', 'val' => 'name');
        }
        if (!isset($optAttr['key'], $optAttr['val'])) {
            $optAttr['key'] = 'id';
            $optAttr['val'] = 'name';
        }
        $optVal = $optAttr['key'];
        $optTxt = $optAttr['val'];
        unset($optAttr['key'], $optAttr['val']);
        foreach ($options as $option) {
            $sel = (in_array($option[$optVal], $selected)) ? ' selected="selected"' : '';

            $form .= '<option value="' . $option[$optVal] . '"' . $sel;
            foreach ($optAttr as $k => $v) {
                $form .= " {$v}=\"{$option[$k]}\"";
            }
            $form .= '>' . $option[$optTxt] . "</option>\n";
        }

        $form .= '</select>';

        return $form;
    }
}

/**
 * Fix text direction according to: document direction[RTL or LTR] and text language[AR,FR,EN],
 * Checks if the text has Arabic characters and if document direction is LTR the correct direction should be RTL
 * Checks if the text is not Arabic and the document direction is RTL, the correct direction is LTR
 * @return $fixedDirection the correct direction
 */
function fix_text_direction($currenctDirection = 'ltr', $text = '')
{
    mb_regex_encoding('UTF-8');
    $fixedDirection = $currenctDirection;
    if ((mb_ereg('[\x{0600}-\x{06FF}]', $text))) {
        if ($currenctDirection == 'ltr') {
            $fixedDirection = 'rtl';
        }
    } elseif ($currenctDirection == 'rtl') {
        $fixedDirection = 'ltr';
    }
    return $fixedDirection;
}

/*
 * function that removes the last character separator in a string
 * $return $text string
 */

function remove_last_character($text, $separator)
{
    if (substr($text, -(strlen($separator) + 1), strlen($separator)) === $separator) {
        $text = substr($text, 0, -(strlen($separator) + 1));
        return $text;
    }
    return $text;
}

function load_excel_data(&$nb_col, $colspan = 0)
{
    $colspan ? $nb_col += $colspan : $nb_col++;
    $array_data['headerBg'] = "#cccccc";   
    $array_data['nb_col'] = $nb_col;
    return $array_data;
}

/*
 * Strip HTML and PHP tags from a sting or array
 * @params $data - string value / array of values
 * @return string / array
 */
function strip_all_tags($data){
    if(is_array($data)){
        foreach($data as $key => $value){
            if(is_string($value)){
                $data[$key] = strip_tags($value);
            }
        }
        return $data;
    }else{
        return strip_tags($data); 
    }
}

function replace_quotes_n_strip_tags($data){
    return str_replace('"', '&quot;',strip_tags($data));
}

if (!function_exists('dd')) {
 function dd()
  {
      echo '<pre>';
      array_map(function($x) { var_dump($x); }, func_get_args());
      die;
   }
}
if (!function_exists('pd')) {
 function pd()
  {
      echo '<pre>';
      array_map(function($x) { print_r($x); }, func_get_args());
      die;
   }
}
