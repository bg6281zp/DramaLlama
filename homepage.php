<?php
/**
 * Created by PhpStorm.
 * User: carlycoughlin
 * Date: 3/26/17
 * Time: 3:13 PM
 */
define('SITE_NAME', 'www.dramallama.com');
define('BASE', str_replace($_SERVER['SERVER_NAME'], '',$_SERVER['DOCUMENT_ROOT'])); //base root directory name
define('BASE_URI', $_SERVER['DOCUMENT_ROOT'].'/'); //HTML directory name
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']. '/'); //our sites full URL Address
define('DOCTYPE','xhtml strict');

class HomePage{

    private $title = '';
    private $description = '';
    private $keywords = '';
    private $robots = true;
    private $doctype = '';
    private $xhtml = true;
    private $charset = 'utf-8';
    private $include = array();
    private $js = array();
    private $jquery = array();
    private $body = '';

    function __construct($title='')
    {
        if (!empty($title)){
            $this->title = $title;
        }
        elseif (defined('SITE_NAME')){
            $this->title = SITE_NAME;
        }
        if (defined('DOCTYPE')){
            list($type, $standard) = explode('  ', DOCTYPE);
            $this->doctype ($type, $standard);
        }
        else{
            $this->doctype ('xhtml', 'strict');
        }
    }

    public function doctype ($type='html', $standard='strict'){
        if(in_array($standard, array('strict', 'transitional', 'frameset'))){
            if ($type == 'html'){
                $this->xhtml = '';
                switch ($standard){
                    case 'strict': $this->doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'; break;
                    case 'transitional': $this->doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'; break;
                    case 'frameset': $this->doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">'; break;
                }
            }
            elseif ($type = 'xhtml') {
                $this->xhtml = ' /';
                switch ($standard) {
                    case 'strict':
                        $this->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
                        break;
                    case 'transitional':
                        $this->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
                        break;
                    case 'frameset':
                        $this->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
                        break;
                }
            }
        }
    }

    public function access ($user, $level=1){
        switch ($user){
            case 'users':
                if (!isset($_SESSION['user_id']))
                    $this->eject('sign_in/');
                break;
            case 'admin':
                if (!isset($_SESSION['admin']) || $_SESSION['admin'] == 0 || $_SESSION['admin'] > $level)
                    $this->eject();
                break;
            case 'others':
                if (isset($_SESSION['user_id']))
                    $this->eject();
                break;
        }

    }
    public function eject ($where='', $msg=''){
        if (stristr($where, BASE_URL)){
            $url = $where;
        }
        else{
            $url = BASE_URL . $where;
        }
        if(ob_get_length()) ob_end_clean();
        if(empty($msg)){
            $url = str_replace('&amp;', '&', $url);
            header("Location: $url");
        }
        else{
            echo '<script type="text/javascript"> var msg = confirm("' . str_replace(array('<br />', '<br>'), "\\n", addslashes($msg)) . '"); if (msg == true) { window.location = "' . $url . '"; } else { window.location = "' . $url . '"; } </script>';
            echo $msg . '<br /><br /><a href="' . $url . '">Click here to continue.</a>';
            }
        exit;
    }
    public function title ($title=''){
        if (!empty($title))
            $this->title = $title;
        return $this->title;
    }
    public function description ($description){
        $this->description = $description;
    }

    public function keywords ($keywords) {
        $this->keywords = $keywords;
    }

    public function robots ($robots) {
        if (is_bool($robots)) $this->robots = $robots;
    }

    public function charset ($charset) {
        $this->charset = $charset;
    }

    public function link ($link, $prepend=false) {
        if (!is_array($link)) $link = array($link);
        if ($prepend) {
            $this->include = array_merge($link, $this->include);
        } else {
            foreach ($link as $value) $this->include[] = $value;
        }
    }
    public function js ($code, $oneliner=true) {
        if ($oneliner) $code = $this->oneliner($code);
        $this->js[] = $code;
    }
    public function body ($body) {
        $this->body = $body;
    }

    public function url ($action='', $url='', $key='', $value=NULL) {
        $protocol = ($_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        if (empty($url)) $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if ($action == 'ampify') return $this->ampify($url);
        $url = str_replace ('&amp;', '&', $url);
        if (empty($action) && empty($key)) { // clean the slate
            $url = explode('?', $url);
            return $url[0]; // no amps to convert
        }
        $fragment = parse_url ($url, PHP_URL_FRAGMENT);
        if (!empty($fragment)) {
            $fragment = '#' . $fragment; // to add on later
            $url = str_replace ($fragment, '', $url);
        }
        if ($key == '#') {
            if ($action == 'delete') $fragment = '';
            elseif ($action == 'add') $fragment = '#' . urlencode($value);
            return $this->ampify($url . $fragment);
        }
        $url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
        $url = substr($url, 0, -1);
        $value = urlencode($value);
        if ($action == 'delete') {
            return $this->ampify($url . $fragment);
        } elseif ($action == 'add') {
            if (strpos($url, '?') === false) {
                return ($url . '?' . $key . '=' . $value . $fragment); // no amps to convert
            } else {
                return $this->ampify($url . '&' . $key . '=' . $value . $fragment);
            }
        }
    }

    public function display ($content='') {
        $html = '';
        $type = ($this->xhtml) ? 'xhtml' : 'html';
        $frameset = false;
        if (strpos($content, '<frame ') !== false) { // Then this is a frameset ...
            $frameset = true;
            $this->doctype($type, 'frameset');
        } elseif (strpos($this->doctype, 'frameset') !== false) { // If we're here then it's not ...
            $this->doctype($type, 'transitional');
        }
        $html .= $this->doctype . "\n";
        $html .= '<html';
        if ($this->xhtml) $html .= ' lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml"';
        $html .= '>' . "\n";
        $html .= '<head>' . "\n";
        $html .= $this->meta_tags();
        $html .= $this->include_scripts();
        $html .= '</head>' . "\n";
        $html .= ($frameset) ? '<frameset' : '<body';
        if (!empty($this->body)) $html .= ' ' . $this->body;
        $html .= '>';
        $html .= "\n  " . trim($content);
        $html .= ($frameset) ? "\n</frameset>" : "\n</body>";
        $html .= "\n</html>";
        if (!$this->xhtml) $html = str_replace(' />', '>', $html);
        return $html;
    }

    private function meta_tags () {
        $tags = '  <meta http-equiv="content-type" content="text/html; charset=' . $this->charset . '" />' . "\n";
        $tags .= '  <title>' . $this->title . '</title>' . "\n";
        $description = (!empty($this->description)) ? $this->description : $this->title;
        $keywords = (!empty($this->keywords)) ? $this->keywords : $this->title;
        $tags .= '  <meta name="description" content="' . $description . '" />' . "\n";
        $tags .= '  <meta name="keywords" content="' . $keywords . '" />' . "\n";
        if (!$this->robots) $tags .= '  <meta name="robots" content="noindex, nofollow" />' . "\n";
        return $tags;
    }

    private function include_scripts () {
        $scripts = $this->combine_scripts($this->sort_scripts($this->include));
        if (!empty($this->jquery)) {
            $this->jquery = array_unique($this->jquery);
            $scripts .= '  <script type="text/javascript">$(document).ready(function(){' . "\n  ";
            $scripts .= implode("\n  ", $this->jquery);
            $scripts .= "\n  })</script>\n";
        }
        return $scripts;
    }

    private function sort_scripts ($array) { // used in $this->include_scripts()
        $array = array_unique($array);
        $scripts = array();
        foreach ($array as $script) {
            $parts = explode('.', $script);
            $ext = array_pop($parts);
            $name = implode('.', $parts);
            switch ($ext) {
                case 'ico': $scripts['ico'] = $script; break;
                case 'css': $scripts['css'][] = $name; break;
                case 'js': $scripts['js'][] = $name; break;
                default: $scripts['other'][] = $script; break;
            }
        }
        return $scripts;
    }

    private function combine_scripts ($sorted) { // used in $this->include_scripts()
        if (empty($sorted)) return '';
        $scripts = array();
        if (isset($sorted['ico'])) {
            $scripts[] = '<link rel="shortcut icon" type="image/x-icon" href="' . $sorted['ico'] . '" />';
        }
        if (isset($sorted['css'])) {
            foreach ($sorted['css'] as $script) {
                $scripts[] = '<link rel="stylesheet" type="text/css" href="' . $script . '.css" />';
            }
        }
        if (isset($sorted['js'])) {
            foreach ($sorted['js'] as $script) {
                $scripts[] = '<script type="text/javascript" src="' . $script . '.js"></script>';
            }
        }
        if (isset($sorted['other'])) $scripts = array_merge($scripts, $sorted['other']);
        return '  ' . implode("\n  ", $scripts) . "\n";
    }

    private function oneliner ($code) {
        return preg_replace('/\s(?=\s)/', '', str_replace(array("\r\n", "\r", "\n"), ' ', $code));
    }

    private function ampify ($string) { // used in $this->url
        return str_replace(array('&amp;', '&'), array('&', '&amp;'), $string);
    }

}


