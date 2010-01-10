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

/**
 * The Log2RssConverter-Manager creates a rss-node structure out of the passed svn-node structure.
 *
 * @author Stefan Idler, sidler@mulchprod.de
 */
class Log2RssConverter {

    /**
     *
     * @var ConfigReader
     */
    private $objConfig;

    public function __construct(ConfigReader $objConfig) {
        $this->objConfig = $objConfig;
    }

    /**
     * Generates a rss-node-structure from the passed log-node-structure
     * @param string $objLogRootNode
     * @return SimpleXMLElement
     */
    public function generateRssNodesFromLogContent($strLogRootNode) {


        $objFeedRootNode = new SimpleXMLElement("<rss version=\"2.0\"></rss>");
        $objChannel = $objFeedRootNode->addChild("channel");
        $objChannel->addChild("title", $this->objConfig->getStrFeedTitle());
        $objChannel->addChild("description", $this->objConfig->getStrFeedDescription());
        $objChannel->addChild("link", $this->objConfig->getStrSvnUrl());
        $objChannel->addChild("pubDate", strftime("%a, %d %b %Y %H:%M:%S GMT", time()));


        //build a xml-tree out of the passed svn-log-content
        libxml_use_internal_errors();
        $objSimpleXmlElement = simplexml_load_string($strLogRootNode);
        $arrParseErrors = libxml_get_errors();
        libxml_clear_errors();

        if(count($arrParseErrors) > 0)
            throw new Svn2RssException("Error parsing xml-based svn log content.\nErrors:\n".implode("\n", $arrParseErrors));

        foreach($objSimpleXmlElement->logentry as $objOneLogEntry) {

            $arrObjAttributes = $objOneLogEntry->attributes();
            $objRssItemNode = $objChannel->addChild("item");

            //prepare log-message
            $strDescription = $objOneLogEntry->msg->__toString();
            $strDescription = html_entity_decode($strDescription, ENT_COMPAT, "UTF-8");
            //but: encode &, <, >
            $strDescription = str_replace(array("&", "<", ">"), array("&amp;", "&lt;", "&gt;"), $strDescription);

            //title, description, logdate
            $objRssItemNode->addChild("title", $arrObjAttributes->revision->__toString()." by ".$objOneLogEntry->author->__toString());
            $objDescNode = $objRssItemNode->addChild("description", $strDescription);
            $objRssItemNode->addChild("pubDate", $objOneLogEntry->date->__toString());
            
        }

        return $objFeedRootNode;

    }


    
}
?>
