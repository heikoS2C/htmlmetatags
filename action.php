<?php
/**
 * DokuWiki Plugin htmlmetatags (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Heiko Heinz <heiko.heinz@soft2c.de>
 * @author  Eric Maeker <eric@maeker.fr>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_htmlmetatags extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {

       // $controller->register_hook('htmlmetatags', 'FIXME', $this, 'handle_htmlmetatags');
       $controller->register_hook('TPL_METAHEADER_OUTPUT','BEFORE',$this,'handle_htmlmetatags',array());
   
    }
/*
    public function searchname($namematch, $meta){
    	for ($i=0;$i<sizeof($meta);$i++) {
    		$a = $meta[$i];
    		if($namematch == $a['name']){
    			return $a;
    		}
    	}
    	 
    	return null;
    }
  */
  
    function replaceMeta(&$pageArray, $name, $value) {
    // Override dokuwiki default meta tags
      $found = False;
      foreach($pageArray['meta'] as $k => $v) {
        if ($v["name"] == $name) {
          $v["content"] = $value;
          $pageArray['meta'][$k] = $v;
          $found = True;
        }
      }
      // If meta not set, add it as name or property
      if (!$found) {
        if (strpos($name, ':') !== false)
          $pageArray['meta'][] = array("property" => $name, "content" => $value);
        else
          $pageArray['meta'][] = array("name" => $name, "content" => $value);
      }
    }

    
    /**
     * [Custom event handler which performs action]
     * Prints keywords to the meta header
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_htmlmetatags(Doku_Event &$event, $param) {
    
      global $ID;
      global $conf;
      if (empty($event->data)) return; // nothing to do for us
      
      $metadata = p_get_metadata($ID);
      
      $a = $metadata["htmlmetatags"];
      if(empty($a)) return;
      
      foreach(array_keys($a) as $cur_key) {
        $this->replaceMeta($event->data, $cur_key, $a[$cur_key]);
      }
    }
    
}

// vim:ts=4:sw=4:et:
