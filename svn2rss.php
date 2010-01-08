<?php

//set up base-constants
define("SVN2RSS_PROJECT_ROOT",  dirname(__FILE__));
define("SVN2RSS_CONFIG_NAME",   "svn2rss.xml");




/**
 * Autloader. Handles the loading of class-definitions not known to the compiler.
 * Called by PHP, so no need to call on your own
 * @param string $strClassName
 * @return void
 */
function __autoload($strClassName) {
    
    if(require(PROJECT_ROOT."/svn2rss/".$strClassName.".php"))
        return;
}

?>
