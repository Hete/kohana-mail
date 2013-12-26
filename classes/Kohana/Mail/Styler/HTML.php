<?php

defined('SYSPATH') or die('No direct script access.');

require_once Kohana::find_file('vendor', 'simplehtmldom/simple_html_dom');

/**
 * HTML styler with CSS.
 *
 * The given mail body must be a valid HTML document.
 *
 * @package Mail
 * @category Stylers
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 */
class Kohana_Mail_Styler_HTML extends Mail_Styler {

    public $content_type = 'text/html';

    public function __construct() {
        spl_autoload_register(array($this, 'auto_load'));
    }

    /**
     * Namespace autoloader for vendor files
     *
     * @param string $class_name
     */
    public function auto_load($class_name) {
        if ($file = Kohana::find_file('vendor/PHP-CSS-Parser/lib', str_replace('\\', '/', $class_name))) {
            require_once $file;
        }
    }

    public function style($body) {
    
        if (Kohana::$profiling === TRUE) {
            $benchmark = Profiler::start(__class__, __function__);
        }

        $css = Kohana::$config->load('mail.styler.HTML.css_file');

        $dom = str_get_html((string) $body);

        $css_parser = new Sabberworm\CSS\Parser(file_get_contents($css));

        $declaration_blocks = $css_parser->parse()->getAllDeclarationBlocks();

        foreach ($declaration_blocks as $declaration_block) {
            foreach ($declaration_block->getSelectors() as $selector) {
                foreach ($dom->find($selector) as $element) {
                    // Inline style overloads style from sheet
                    $element->style = implode('', $declaration_block->getRules()) . $element->style;
                }
            }
        }

        $dom = (string) $dom;

        if (isset($benchmark)) {
            Profiler::stop($benchmark);    
        }

        return $dom;

        return (string) $dom;
    }

}
