<?php

namespace Page;

use ArrayIterator;
use Exception;
use tidy;

//ini_set('display_errors', '0');
class GetPageClass {

    const WEBBOT_NAME = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0";
    const CURL_TIMEOUT = 40;
    const HEAD = "HEAD";
    const GET = "GET";
    const POST = "POST";
    const POSTMULTI = "POSTMULTI";
    const EXCL_HEAD = FALSE;
    const INCL_HEAD = TRUE;

    public $dataSearch = array();
    public $defaultCurlCoding = 'utf8';
    public $objTidy = null;
    public $randomIdBt = '';
    public $cookie = "blank.txt";
    public $hostUrl = '';
    public $adresgetpage = '';
    public $config = array('indent-spaces' => false, 'wrap' => 0, 'tab-size' => 4, 'char-encoding' => 'asci', 'input-encoding' => 'utf8', 'output-encoding' => 'utf8', 'newline' => false, 'doctype-mode' => false, 'doctype' => '', 'repeated-attributes' => false, 'alt-text' => '', 'slide-style' => '', 'error-file' => '', 'output-file' => '', 'write-back' => false, 'markup' => true, 'show-warnings' => true, 'quiet' => false, 'indent' => false, 'hide-endtags' => false, 'input-xml' => false, 'output-xml' => false, 'output-xhtml' => true, 'output-html' => false, 'add-xml-decl' => false, 'uppercase-tags' => false, 'uppercase-attributes' => false, 'bare' => false, 'clean' => true, 'logical-emphasis' => true, 'drop-proprietary-attributes' => false, 'drop-font-tags' => false, 'drop-empty-paras' => true, 'fix-bad-comments' => true, 'break-before-br' => false, 'split' => false, 'numeric-entities' => false, 'quote-marks' => true, 'quote-nbsp' => true, 'quote-ampersand' => true, 'wrap-attributes' => false, 'wrap-script-literals' => true, 'wrap-sections' => false, 'wrap-asp' => false, 'wrap-jste' => true, 'wrap-php' => true, 'fix-backslash' => true, 'indent-attributes' => false, 'assume-xml-procins' => false, 'add-xml-space' => false, 'enclose-text' => false, 'enclose-block-text' => false, 'keep-time' => false, 'word-2000' => true, 'tidy-mark' => false, 'gnu-emacs' => false, 'gnu-emacs-file' => '', 'literal-attributes' => false, 'show-body-only' => true, 'fix-uri' => true, 'lower-literals' => true, 'hide-comments' => false, 'indent-cdata' => true, 'force-output' => true, 'show-errors' => 6, 'ascii-chars' => false, 'join-classes' => false, 'join-styles' => true, 'escape-cdata' => false, 'language' => 'pl', 'ncr' => true, 'output-bom' => 2, 'replace-color' => false, 'css-prefix' => '', 'new-inline-tags' => 'cfif, cfelse, math, mroot, mrow, mi, mn, mo, msqrt, mfrac, msubsup, munderover, munder, mover, mmultiscripts, msup, msub, mtext, mprescripts, mtable, mtr, mtd, mth, new-blocklevel-tags:, cfoutput, cfquery', 'new-blocklevel-tags' => '', 'new-empty-tags' => 'cfelse', 'new-pre-tags' => '', 'accessibility-check' => false, 'vertical-space' => false, 'punctuation-wrap' => false, 'merge-divs' => 2, 'decorate-inferred-ul' => false, 'preserve-entities' => false, 'sort-attributes' => false, 'merge-spans' => 2, 'anchor-as-name' => true,);
    private $httpHeader;
    private $referer;

    /**
     * 
     * @param array $config ['url'=>'','cookie'=>'',]
     * @throws canstructException
     * @param array $config ['url'=>'','cookie'=>'',]
     * @throws canstructException
     * 
     */
    function __construct($config = ['url' => '', 'cookie' => '']) {
        try {
            if (is_array($config)) {
                try {
                    if (!array_key_exists('url', $config) || $config['url'] == null || $config['url'] == '') {
                        throw new canstructException("Missing config array key:url");
                    }
                } catch (canstructException $e) {
                    echo($e->errorMessage());
//                    echo($e->getTraceAsString());
                }

                try {
                    if (array_key_exists('cookie', $config) && $config['cookie'] !== '') {
                        $this->randomIdBt = 'bt_add_' . rand(0, 10000);
                        $this->adresgetpage = $config['url'];
                        $this->cookie = $config['cookie'];
                        if ($this->cookie == '' || $this->cookie == null) {
                            $this->cookie = "blank.txt";
                        }
                        $this->cookie = $this->cookiePath();
                    } else {
                        throw new canstructException("Brakuje cookie like my-id.txt");
                    }
                } catch (canstructException $exc) {
                    echo $exc->errorMessage();
                }
            } else {
                throw new canstructException("Nieprawidlowa tablica");
            }
        } catch (canstructException $e) {
            echo $e->errorMessage();
        }
    }

    function dataSearchCount($key) {
        if (!is_string($key) || count($this->dataSearch) < 1) {
            return;
        }

        $arr = new ArrayIterator($this->dataSearch);
        $data = $arr->offsetGet($key);
        $c = count($data);
        return [$c, $data, $arr];
    }

    function findRegEx($node, $tagName, $value, $AttrName, $AttrValue, $collectionKey) {
        if ($node->hasChildren()) {
            foreach ($node->child as $child) {
                $this->findRegEx($child, $tagName, $value, $AttrName, $AttrValue, $collectionKey);
                if ($tagName !== '' && $child->name == $tagName) {
                    if ($value !== '' && $AttrName !== '' && @ array_key_exists($AttrName, $child->attribute) && @ preg_match($value, $child->value)) {
                        if ($AttrValue !== '' && @ preg_match($AttrValue, $child->attribute[$AttrName])) {
                            $this->dataSearch[$collectionKey][] = $child;
                        } elseif ($AttrValue == '') {
                            $this->dataSearch[$collectionKey][] = $child;
                        }
                    } else
                    if ($value == '' && $AttrName !== '' && @ array_key_exists($AttrName, $child->attribute)) {
                        if ($AttrValue !== '' && @ preg_match($AttrValue, $child->attribute[$AttrName])) {
                            $this->dataSearch[$collectionKey][] = $child;
                        } elseif ($AttrValue == '') {
                            $this->dataSearch[$collectionKey][] = $child;
                        }
                    } else
                    if ($value !== '' && $AttrName == '' && @ preg_match($value, $child->value)) {
                        $this->dataSearch[$collectionKey][] = $child;
                    } else
                    if ($AttrName == '' && $value == '') {
                        $this->dataSearch[$collectionKey][] = $child;
                    }
                }
            }
        }
    }

    function os() {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match("/windows/i", $agent))
            return "Windows'a";
        if (preg_match("/linux/i", $agent))
            return "Linux'a";
        if (preg_match("/mac/i", $agent))
            return "Mac'a";
        if (preg_match("/BeOS/i", $agent))
            return "BeOS'a";
        if (preg_match("/OS\/2/i", $agent))
            return "OS/2";
        return "forumweb";
    }

    function cookiePath() {
        $this->hostUrl = $this->domena($this->adresgetpage);

        if (strstr($this->os(), "indow")) {
            return __DIR__ . "\\cookies\\" . $this->hostName() . '-' . $this->cookie;
        } else {
            return __DIR__ . "/cookies/" . $this->hostName() . '-' . $this->cookie;
        }
    }

    function GoPage() {
        $downloadPage = $this->http_get_withheader($this->adresgetpage, $this->adresgetpage);
        $tidy = @ tidy_parse_string($downloadPage['FILE'], $this->config, $this->defaultCurlCoding);
        $this->objTidy = $tidy;
        return $tidy;
    }

    function getBody() {
        if ($this->objTidy instanceof tidy) {
            return $this->objTidy->value->body();
        }
        return null;
    }

    function domena($url) {
        $taburl = parse_url($url);
        return $taburl['scheme'] . "://" . $taburl['host'];
    }

    function hostName() {
        return parse_url($this->adresgetpage, PHP_URL_HOST);
    }

    function getChildrenList($parent, $arr_HtmlNames, $method, $objTidy) {
        if (is_object($objTidy)) {
//			var_dump($objTidy);
            $wy = null;
            //$objTidy=$objTidy->body();
            if (is_array($parent)) {
                $parentname = $parent['name'];
                $_GET[$parentname] = null;
                $this->findRegEx($objTidy, $parentname, $parent['value_rgx'], $parent[
                        'attribute'], $parent['attribute_rgx'], $parentname);
                if ($_GET[$parentname][0] !== null) {
                    $licznik = -1;
                    foreach ($_GET[$parentname] as $parents) {
                        ++$licznik;
                        if ($parents->hasChildren()) {
                            if (is_array($arr_HtmlNames)) {
                                $ll = -1;
                                foreach ($parents->child as $childs) {
                                    ++$ll;
                                    if (array_key_exists($childs->name, $arr_HtmlNames)) {
                                        foreach ($arr_HtmlNames as $val) {
                                            $attributechild = $val['atr'];
                                            if (
                                                    preg_match($val['val_rgx'], $childs->value) || (array_key_exists($val['atr'], $childs->attribute) && preg_match($val['atr_rgx'], $childs->attribute[$attributechild])
                                                    )
                                            ) {
                                                $fl = true;
                                                break;
                                            }
                                        }
                                        if ($fl == true)
                                            $wy[] = $this->$method($childs, $licznik . $ll);
                                    }
                                }
                            }
                        }
                    }
                }
            } else
                return 'parent nie jest tablicą';
            return join("", $wy);
        } else
            return 'getChildrenList(parent,arr_HtmlNames,method,objTidy)  objTidy to nie obiekt';
    }

    function out($objTidyNode) {
        if (is_object($objTidyNode)) {
            return $objTidyNode->attribute['id'];
        } else
            return 'blad';
    }

    function convNewToStr($arrReturnParseNew) {
        if (is_array($arrReturnParseNew)) {
            foreach ($arrReturnParseNew as $tabele) {
                $out .= $tabele;
            }
            return $out;
        } else
            return 'wyjsciez glownej funkcji fase';
    }

    function parseNew($objTidy, $parent, $child) {
        if (is_object($objTidy)) {
            $wy = null;
            $objTidy = $objTidy->body();
            if (is_array($parent) || $parent == null) {
                if (is_array($parent)) {
                    $parentname = $parent['name'];
                    $_GET[$parentname] = null;
                    $this->findRegEx($objTidy, $parentname, $parent['value_rgx'], $parent['attribute'], $parent['attribute_rgx'], $parentname);
                    if ($_GET[$parentname][0] !== null) {
                        foreach ($_GET[$parentname] as $parents) {
                            if (is_array($child)) {
                                $childname = $child['name'];
                                $_GET[$childname] = null;
                                $this->findRegEx($parents, $childname, $child['value_rgx'], $child['attribute'], $child['attribute_rgx'], $childname);
                                if ($_GET[$childname][0] !== null) {
                                    $attribute = $parent['attribute'];
                                    $attribute_rgx = $parent['attribute_rgx'];
                                    $name = $parent['name'];
                                    switch ($attribute) {
                                        case (null || ''):foreach ($_GET[$childname] as $childs) {
                                                if ($childs->getParent()->name === $name && preg_match($attribute_rgx, $childs->getParent()->attribute[$attribute])) {
                                                    $wy[] = $this->out($childs);
                                                }
                                            }
                                            break;
                                        case (!null && !''):foreach ($_GET[$childname] as $childs) {
                                                if ($childs->getParent()->name === $name) {
                                                    $wy[] = $this->out($childs);
                                                }
                                            }
                                            break;
                                        default:
                                            foreach ($_GET[$childname] as $childs) {
                                                if ($childs->getParent()->name === $name && preg_match($attribute_rgx, $childs->getParent()->attribute[$attribute])) {
                                                    $wy[] = $this->out($childs);
                                                }
                                            }
                                            break;
                                    }
                                } elseif ($child == NULL || $_GET[$childname] == null) {
                                    $wy = false;
                                }
                            }
                        }
                    }
                } elseif ($parent == null) {
                    if (is_array($child)) {
                        $childname = $child['name'];
                        $_GET[$childname] = null;
                        $this->findRegEx($parents, $childname, $child['value_rgx'], $child['attribute'], $child['attribute_rgx'], $childname);
                        if ($_GET[$childname][0] !== null) {
                            foreach ($_GET[$childname] as $childs) {
                                $wy[] = $this->out($childs);
                            }
                        } elseif ($child == NULL || $_GET[$childname] == null) {
                            $wy = false;
                        }
                    } elseif ($child == null) {
                        $wy = false;
                    }
                }
            } else {
                $wy = false;
            }
            $wy = $wy;
        } else {
            $wy = 'nie objekt';
        }
        return $this->convNewToStr($wy);
    }

    function http($httpHeader = [], $cookie, $target, $ref, $method, $data_array, $incl_head) {
        $ch = curl_init();
        if (is_array($data_array)) {
            foreach ($data_array as $key => $value) {
                if (strlen(trim($value)) > 0) {
                    $temp_string[] = $key . "=" . urlencode($value);
                } else {
                    $temp_string[] = $key;
                }
            }
            $query_string = join('&', $temp_string);
        }
        if ($method == self :: HEAD) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        } else {
            if ($method == self :: GET) {
                if (isset($query_string)) {
                    $target = $target . "?" . $query_string;
                    curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                    curl_setopt($ch, CURLOPT_POST, FALSE);

                    if (is_array($type)) {
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $type);
                    }
                }
            }
            if ($method == self :: POST) {
                if (isset($query_string)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
                }
            }
            curl_setopt($ch, CURLOPT_HEADER, $incl_head);
            curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        }
        if (is_array($data_array)) {
            foreach ($data_array as $key => $value) {
                if (strlen(trim($value)) > 0)
                    $temp_string[] = $key . "=" . urlencode($value);
                else
                    $temp_string[] = $key;
            }
            $query_string = join('&', $temp_string);
        }
        if ($method == self :: HEAD) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        } else {
            if ($method == self :: GET) {
                if (isset($query_string)) {
                    $target = $target . "?" . $query_string;
                    curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                    curl_setopt($ch, CURLOPT_POST, FALSE);
                }
            }
            if ($method == self :: POST) {
                if (isset($query_string)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
                }
            }
            if ($method == self :: POSTMULTI) {
                if (isset($data_array)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
                    curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
                }
            }
            curl_setopt($ch, CURLOPT_HEADER, $incl_head);
            curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);
        curl_setopt($ch, CURLOPT_USERAGENT, self :: WEBBOT_NAME);
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_REFERER, $ref);
        curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return_array['FILE'] = curl_exec($ch);
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR'] = curl_error($ch);
        curl_close($ch);
        return $return_array;
    }

    function getDataSearch() {
        return $this->dataSearch;
    }

    function getDataSearchKey($key) {
        if (!is_string($key)) {
            return NULL;
        }

        return $this->dataSearch[$key];
    }

    function getDefaultCurlCoding() {
        return $this->defaultCurlCoding;
    }

    function getObjTidy() {
        return $this->objTidy;
    }

    function getCookie() {
        return $this->cookie;
    }

    function getAdresgetpage() {
        return $this->adresgetpage;
    }

    function getConfig() {
        return $this->config;
    }

    function setDataSearch($dataSearch) {
        $this->dataSearch = $dataSearch;
        return $this;
    }

    function setDefaultCurlCoding($defaultCurlCoding) {
        $this->defaultCurlCoding = $defaultCurlCoding;
        return $this;
    }

    function setObjTidy($objTidy) {
        $this->objTidy = $objTidy;
        return $this;
    }

    function setCookie($cookie) {
        $this->cookie = $cookie;
        return $this;
    }

    function setAdresgetpage($adresgetpage) {
        $this->adresgetpage = $adresgetpage;
        return $this;
    }

    function setConfig($config) {
        $this->config = $config;
        return $this;
    }

    function http_get($target, $ref) {
        if (($target == '' && $ref == null) && $this->adresgetpage !== '') {
            $target = $this->adresgetpage;
            $ref = $this->adresgetpage;
        }

        return $this->http($httpHeader, $this->cookie, $target, $ref, $method = "GET", $data_array = "", self :: INCL_HEAD);
    }

    function http_getnohead($target, $ref) {
        return $this->http($httpHeader, $this->cookie, $target, $ref, $method = "GET", $data_array = "", self :: EXCL_HEAD);
    }

    function http_get_withheader($target, $ref) {
        $httpHeader = $this->getHttpHeader();
        return $this->http($httpHeader, $this->cookie, $target, $ref, $method = "GET", $data_array = "", self :: INCL_HEAD);
    }

    function http_get_form($target, $ref, $data_array) {
        return $this->http($httpHeader, $this->cookie, $target, $ref, $method = self :: GET, $data_array, self :: EXCL_HEAD);
    }

    function http_get_form_withheader($target, $ref, $data_array) {
        return $this->http($httpHeader, $this->cookie, $target, $ref, $method = self :: GET, $data_array, self :: INCL_HEAD);
    }

    function http_post_form($target, $ref, $data_array) {
        return $this->http($httpHeader, $this->cookie, $target, $ref, $method = self :: POST, $data_array, self :: EXCL_HEAD);
    }

    function http_post_withheader($target, $ref, $data_array) {
        return $this->http($httpHeader, $this->cookie, $target, $ref, $method = self :: POST, $data_array, self :: INCL_HEAD);
    }

    function http_post_form_multi($target, $ref, $data_array) {
        return http($httpHeader, $this->cookie, $target, $ref, $method = self :: POSTMULTI, $data_array, self :: EXCL_HEAD);
    }

    function http_header($target, $ref) {
        return $this->http($httpHeader, $this->cookie, $target, $ref, $method = "HEAD", $data_array = "", self :: INCL_HEAD);
    }

    public function setHttpHeader($httpHeader) {
        $this->httpHeader = $httpHeader;
        return $this;
    }

    public function getHttpHeader() {
        return $this->httpHeader;
    }

    public function getReferer() {
        return $this->referer;
    }

    public function setReferer($referer) {
        $this->referer = $referer;
    }

}

class canstructException extends Exception {

    public function errorMessage() {
        $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile()
                . ': <b>' . $this->getMessage() . '</b> Constructor need array witch url and cookie name.';
        return $errorMsg;
    }

}
