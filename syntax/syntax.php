<?php
/**
 * DokuWiki Plugin htmlmetatags (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Heiko Heinz <heiko.heinz@soft2c.de>
 * @author  Eric Maeker <eric@maeker.fr>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_htmlmetatags_syntax extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'block';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 110;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
    if ($mode == 'base'){
          $this->Lexer->addSpecialPattern('{{htmlmetatags>.+?}}',$mode,'plugin_htmlmetatags_syntax');
      }
//        $this->Lexer->addEntryPattern('<FIXME>',$mode,'plugin_htmlmetatags_syntax');
    }

//    public function postConnect() {
//        $this->Lexer->addExitPattern('</FIXME>','plugin_htmlmetatags_syntax');
//    }

    /**
     * Handle matches of the htmlmetatags syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler){
        // Remove all linefeeds before parsing attributes
        $match = str_replace(PHP_EOL,"",$match);
        
        // remove the plugin-activator string
        $match = str_replace("{{htmlmetatags>","",$match);
        $match = str_replace("}}","",$match);
        
        // Explode match into attributes array using 'metatag-' as mask
        return explode("metatag-", $match);
    }

   /**
     * Render xhtml output or metadata
     *
     * usage: {{htmlmetatags>metatag-keywords:(apfel,bananne,birne) metatag-description:(Allgemeiner Obstbauer)}}
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
 
    	global $ID;
 
        switch ($mode) {
          case 'metadata' :
              /* 
               * e.g.
               * data[0]="keywords=(apfel, bananne, birne) "
               * data[1]="og:description=Allgemeiner Obstbauer"
               */
              for ($i=0;$i<sizeof($data);$i++) {
                 $mt = explode("=", $data[$i]);
                 $size = sizeof($mt);
 
                 // If attributes as value
                 if(sizeof($mt)==2){
                 	  $name = trim($mt[0]);
                 	  $content = trim(preg_replace("/\((.*?)\)\s*$/","\\1",$mt[1]));
                 	  // Test if attribute name is a media files and get media file absolute URL
                 	  if (substr($name, 0, 6) === 'media-') {
                 	      $name = substr($name, 6);
                 	      $content = ml($content, '', true, '&amp;', true);
                    }
                    // Send result to renderer
                    if (!empty($content)) {
                      if ($name == "keywords") {
                        if (!empty($renderer->meta['htmlmetatags'][$name]))
                          $renderer->meta["htmlmetatags"][$name] .= ', '.$content;
                        else
                          $renderer->meta["htmlmetatags"][$name] .= $content;
                      }
                      else
                        $renderer->meta["htmlmetatags"][$name] .= $content;
                    }
                 }
              }
              return true;
        }
 
        return false;
    }
}

// vim:ts=4:sw=4:et:
