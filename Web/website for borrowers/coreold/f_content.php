<?php

/* ФУНКЦИИ РАБОТЫ С ТЕКСТОВЫМ КОНТЕНТОМ */

// Вставка выполняемого кода или виджета с передачей массива данных внутрь него
// Переданный массив данных доступен внутри виджета как массив $v
// Если передан не массив, а единственная переменная, то она доступна как $v, так и как $content
// Формат файла - строго .php (окончание '.php' при вызове функции можно опустить)
function execute($widget_path, $content = false) {
    global $page, $app, $user, $self,$countrym;
    $type = substr($widget_path, (strlen($widget_path) - 4), 4);
    if ($type != '.php') $widget_path = trim($widget_path) . '.php';
    if (is_array($content)) extract($content);
    $full_path = MC_ROOT . '/templates/' . $widget_path;
    if ($app['mode'] == 'debug') require $full_path;
    else @require $full_path;
}

// Рендер файла с передачей массива данных внутрь него
// Значения в переданном ассоциативном массиве заменяют вставки [[name]] в файле, где name - имя ключа массива
// т.е. если в переданном массиве $array['foo'] = '123', то [[foo]] при рендеринге будет заменено на 123.
// Формат (расширение) файла может быть любой - js, html и пр.
function render($file_path, $v = []) {
    global $page, $app, $user, $self;
    if (strpos($file_path, '.') === false) $file_path = trim($file_path) . '.php';
    $file_content = file_get_contents(MC_ROOT . '/templates/' . $file_path);
    
	/*
	// Обработка вставок вида [[name]]
	preg_match_all('/\/?\*?\[\[\s+([\w-_]+)\s+\]\]\*?\/?/i', $file_content, $matches);
    //dd($matches);
    if (!empty($matches[1]))
        foreach ($matches[1] as $num => $name)
            $file_content = str_replace($matches[0][$num], $v[$name], $file_content);
    */
	return $file_content;
}

// Вывод дампа переменной и остановка выполнения программы
function dd($variable) {
    die ( nl2br( print_r( $variable, true ) ) );
}

// Вывод дампа переменной и остановка выполнения программы
function dnd($variable) {
    echo ( nl2br( print_r( $variable, true ) ) );
}

// Подключение дополнительного ресурса с проверкой на дублирование
function resource($path, $type = false) {
    global $page, $app, $user, $self;
    if (!is_array($path)) $paths = [$path];
    else $paths = $path;
    foreach ($paths as $path) {
        // Если тип ресурса не указан, пытаемся определить его самостоятельно по последним 3 символам
        if ($type === false) $type_ = substr($path, (strlen($path) - 3), 3);
        else $type_ = $type;
        $type_ = strtolower($type_);
        switch ($type_) {
            case 'css':
                if (array_search($path, $page['css']) === false) $page['css'][] = $path; break;

            case 'js':
            case '.js':
                if (array_search($path, $page['js']) === false) $page['js'][] = $path; break;

            default: /* Подключение скрипта из переменной, а не в виде файла */
                $page['js_raw'] .= "$path\r\n";
        }
    }
}

// Вывод списка подключаемых стилей

function getParOut($url){
	$gom=explode('?',$url); return $gom[0];
}

function css_resources() {
    global $page, $app, $self;	 $r = '';
    $all_css=[]; //array will all css for minification
    // Подключение дополнительных стилей для страницы, если они объявлены
    if (count($page['css'])) {
        foreach ($page['css'] as $css) {
            if (substr($css, 0, 4) == 'http') $r .= "<link rel=\"stylesheet\" href=\"$css\">\r\n";
            else {
				$urlget=''; $gom=explode('?',$css); if (isset($gom[1])) {$css=$gom[0]; $urlget='?'.$gom[1];  } 
                if (substr($css, 0, 1) == '/') {
                    $css_path1 = $css;
                    $css_path2 = "/assets" . $css;
                } else {
                    $css_path1 = "/assets/css/" . $css;
                    $css_path2 = "/vendor/" . $css;
                }
                if (file_exists( MC_ROOT . $css_path1 ))
                {
                    if($app['mode']=='debug') $r .= '<link rel="stylesheet" href="'.$css_path1.$urlget.'">';
                    else
                    {
                        $all_css['files'][]=$css_path1.$urlget;
                        $all_css['names'][]=$css.$urlget;
                    }
                }
                else if (file_exists( MC_ROOT . $css_path2 ))
                {
                    if($app['mode']=='debug') $r .= '<link rel="stylesheet" href="'.$css_path2.$urlget.'">';
                    else
                    {
                        $all_css['files'][]=$css_path2.$urlget;
                        $all_css['names'][]=$css.$urlget;
                    }
                }
            }
        }
    }
    // Подключение индивидуального стиля с именем, равным имени страницы, если такой существует
    $self_css=substr($self, 0, strlen($self) - 4).".css";
    $css_path = "/assets/css/" . $self_css ;
    if (file_exists( MC_ROOT . $css_path ))
    {
        if($app['mode']=='debug') {
            $r .= '<link rel="stylesheet" href="' . $css_path . '">';
            $r .= '<link rel="stylesheet" href="' . $self_css . '">';
        } else {
            $all_css['files'][]=$css_path;
            $all_css['names'][]=$self_css;
        }
    }

    if($app['mode']=='debug') return $r;

    return '<link rel="stylesheet" href="'.minification($all_css,'css').'">';
}

// Вывод списка подключаемых JS
function js_resources() {
    global $page, $self, $app;
    $r = '';
    $all_js=[]; //array will all js for minification
    // Подключение дополнительных скриптов для страницы, если они объявлены
    if (count($page['js'])) {
        foreach ($page['js'] as $script) {
            if (substr($script, 0, 4) == 'http') $r .= "<script src=\"$script\"></script>\r\n";
            else {
				$urlget=''; $gom=explode('?',$script); if (isset($gom[1])) {$script=$gom[0]; $urlget='?'.$gom[1];  } 
                if (substr($script, 0, 1) == '/') {
                    $script_path1 = $script;
                    $script_path2 = "/assets" . $script;
                } else {
                    $script_path1 = "/assets/js/" . $script;
                    $script_path2 = "/vendor/" . $script;
                }
                if (file_exists(MC_ROOT . $script_path1))
                {
                    if($app['mode']=='debug') $r .= "<script src=\"$script_path1\"></script>\r\n";
                    else
                    {
                        $all_js['files'][]=$script_path1;
                        $all_js['names'][]=$script;
                    }
                }
                else if (file_exists(MC_ROOT . $script_path2))
                {
                    if($app['mode']=='debug') $r .= "<script src=\"$script_path2\"></script>\r\n";
                    else
                    {
                        $all_js['files'][]=$script_path2;
                        $all_js['names'][]=$script;
                    }
                }
            }
        }
    }
    // Подключение индивидуального скрипта с именем, равным имени страницы, если такой существует
    $self_js = substr($self, 0, strlen($self) - 4) . ".js";
    $script_path = "/assets/js/" . $self_js;
    if (file_exists( MC_ROOT . $script_path ))
    {
        if($app['mode']=='debug') {
            $r .= "<script src=\"$script_path\"></script>\r\n";
            $r .= "<script src=\"$self_js\"></script>\r\n";
        } else {
            $all_js['files'][]=$script_path;
            $all_js['names'][]=$self_js;
        }
    }
    // js, не подключаемый из файла, а генерируемый "на лету"
    if (!empty($page['js_raw'])) {
        $r .= '<script type="text/javascript">' . "\r\n";
        $r .= $page['js_raw'];
        $r .= "</script>\r\n";
    }

    if($app['mode']=='debug') return $r;

    return '<script type="text/javascript" src="'.minification($all_js,'js').'"></script>';
}

//self-made minification for both jss & css (with combine.php plugin)
function minification($array,$type)
{
    global $app;
    $new_name=implode('',$array['names']);
    $minify_new_name=date('Ymd_Hms').'_'.md5($new_name).'.'.$type;

    $filename=MC_ROOT.'/tmp/cache_table';
    if($app['os_windows']) $filename=  str_replace ('/','\\',$filename);
    $handle = fopen($filename,'c+b');
    if(filesize($filename)>0)
    {
        $contents = fread($handle, filesize($filename));
        $cache_table = unserialize($contents);
        if(array_key_exists ( $new_name , $cache_table ) !== false)
        {
            $minify_new_name = $cache_table[$new_name];
        }
        else {
            $cache_table[$new_name] = $minify_new_name;
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, serialize($cache_table));
        }
    }
    else {
        fwrite($handle, serialize(array($new_name=>$minify_new_name)));
    }
    fclose($handle);

    if($type=='js') $type='javascript';

    return combine($array,$minify_new_name,$type);
}
