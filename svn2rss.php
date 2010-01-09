<?php
/*
 *   This file is part of svn2rss.
 *
 *   svn2rss is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   svn2rss is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with svn2rss.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 *   (c) Stefan Idler, MulchProductions, sidler@mulchprod.de, http://www.mulchprod.de  
 *
 */


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
