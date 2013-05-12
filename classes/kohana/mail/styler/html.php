<?php

defined('SYSPATH') or die('No direct script access.');

require_once Kohana::find_file('vendor', 'simplehtmldom/simple_html_dom');

/**
 * 
 *
 * @package Mail
 * @category Stylers
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 */
class Kohana_Mail_Styler_HTML extends Mail_Styler {

    private $html,
            $css;

    public function __construct() {
        spl_autoload_register(array($this, 'auto_load'));
    }

    /**
     * Namespace autoloader for vendor files
     * @param type $class_name
     */
    public function auto_load($class_name) {
        if ($file = Kohana::find_file("vendor/PHP-CSS-Parser/lib", str_replace("\\", "/", $class_name))) {
            require_once $file;
        }
    }

    public function content($content) {

        $this->html = str_get_html((string) $content);

        return $this;
    }

    public function style($style) {

        // Parse the css
        $css_parser = new Sabberworm\CSS\Parser((string) $style);
        $this->css = $css_parser->parse();

        return $this;
    }

    public function render() {

        if ($this->css === NULL) {
            return (string) $this->html;
        }

        $declaration_blocks = $this->css->getAllDeclarationBlocks();

        foreach ($declaration_blocks as $declaration_block) {
            foreach ($declaration_block->getSelectors() as $selector) {
                foreach ($this->html->find($selector) as $element) {
                    // Inline style overloads style from sheet
                    $element->style = implode("", $declaration_block->getRules()) . $element->style;
                }
            }
        }

        return (string) $this->html;
    }

}

?>
