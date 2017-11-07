<?php
/**
 * Validates tel (for phone)
 * @todo Validate the email address
 * @todo Filter allowed query parameters
 */

class HTMLPurifier_ConfigExt extends \HTMLPurifier_Config {
    public $secret_key = null;
    public $custom_params= null;
}

class HTMLPurifier_URIScheme_tel extends \HTMLPurifier_URIScheme {
    public $default_port = null;
    public $browsable = true;
    public $hierarchical = true;
    public $may_omit_host = true;
    public function doValidate(&$uri, $config, $context) {
        //Optionally we could validate phone numbers here
        $uri->userinfo = null;
        $uri->host     = null;
        $uri->port     = null;
        $uri->query    = null;
        if (preg_match('/^[+]{1}\d{11,}$/ui', $uri->path)) {
            return true;
        }
        else {
            return false;
        }
    }
}

class HTMLPurifier_URIFilter_MakeRedirect extends \HTMLPurifier_URIFilter
{
    /**
     * @type string
     */
    public $name = 'MakeRedirect';

    /**
     * @type array
     */
    protected $ourHostParts = false;

    /**
     * @param HTMLPurifier_Config $config
     * @return void
     */
    public function prepare($config)
    {
        $our_host = $config->getDefinition('URI')->host;
        if ($our_host !== null) {
            $this->ourHostParts = array_reverse(explode('.', $our_host));
        }
    }

    /**
     * @param HTMLPurifier_URI $uri Reference
     * @param HTMLPurifier_ConfigExt $config
     * @param HTMLPurifier_Context $context
     * @return bool
     */
    public function filter(&$uri, $config, $context)
    {
        if (is_null($uri->host)) {
            return true;
        }
        if ($this->ourHostParts === false) {
            return false;
        }

        $host_parts = array_reverse(explode('.', $uri->host));
        foreach ($this->ourHostParts as $i => $x) {
            if (!isset($host_parts[$i]) || $host_parts[$i] != $this->ourHostParts[$i]) {
                $path = $config->custom_params['redirectUrl'];
                $query = 'url='.urlencode($uri->toString()).'&checksum='.hash_hmac("sha256", $uri->toString(), $config->secret_key);
                $uri = new HTMLPurifier_URI('http',
                    null,
                    trim(preg_replace('/^http(s)?:\/\//ui', '', $config->get('URI.Host')),'/'),
                    null,
                    $path,
                    $query,
                    null
                );
                break;
            }
        }
        return true;
    }
}


class HTMLPurifier_HTMLModule_TargetBlankAll extends \HTMLPurifier_HTMLModule
{

    public $name = 'TargetBlankAll';

    /**
     * @param HTMLPurifier_ConfigExt $config
     */
    public function setup($config) {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new HTMLPurifier_AttrTransform_TargetBlankAll();
    }

}

class HTMLPurifier_AttrTransform_TargetBlankAll extends \HTMLPurifier_AttrTransform_TargetBlank {
    /**
     * @type HTMLPurifier_URIParser
     */
    private $parser;

    public function __construct()
    {
        $this->parser = new HTMLPurifier_URIParser();
    }

    /**
     * @param array $attr
     * @param HTMLPurifier_ConfigExt $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['href'])) {
            return $attr;
        }
        $url = $this->parser->parse($attr['href']);
        if ($url->path==$config->custom_params['redirectUrl']) {
            $attr['target'] = '_blank';
        }
        return $attr;
    }
}

class HTMLPurifier_AttrDef_HTML_Class_Exp extends \HTMLPurifier_AttrDef_HTML_Class {

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    protected function split($string, $config, $context)
    {
        // really, this twiddle should be lazy loaded
        $name = $config->getDefinition('HTML')->doctype->name;
        if ($name == "XHTML 1.1" || $name == "XHTML 2.0") {
            return parent::split($string, $config, $context);
        } else {
            return preg_split('/\s+/', $string);
        }
    }

    /**
     * @param array $tokens
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    protected function filter($tokens, $config, $context)
    {
        $allowed = $config->get('Attr.AllowedClasses');
        $forbidden = $config->get('Attr.ForbiddenClasses');
        $ret = array();
        foreach ($tokens as $token) {
            if (($allowed === null || isset($allowed[$token])) &&
                !isset($forbidden[$token]) &&
                // We need this O(n) check because of PHP's array
                // implementation that casts -0 to 0.
                !in_array($token, $ret, true)
            ) {
                $ret[] = $token;
            }
            else {
                foreach ($allowed as $a=>$f) {
                    $a=str_replace('*', '[a-z0-9-_]*' , $a);
                    if (preg_match('/^'.$a.'$/ui', $token) && !isset($forbidden[$token])) {
                        $ret[] = $token;
                    }
                }
            }
        }
        return $ret;
    }


}