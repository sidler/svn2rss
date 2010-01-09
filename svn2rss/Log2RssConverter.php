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
 * Please note, that only the list of items is being generated, not the wrapping RSS-structure
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
     * @return array an array of SimpleXMLElements
     */
    public function generateRssNodesFromLogContent($strLogRootNode) {
        $arrRssNodeList = array();


        $objRssRootNode = new SimpleXMLElement("<items></items>");

        //build a xml-tree out of the passed svn-log-content
        libxml_use_internal_errors();
        $objSimpleXmlElement = simplexml_load_string($strLogRootNode);
        $arrParseErrors = libxml_get_errors();
        libxml_clear_errors();

        if(count($arrParseErrors) > 0)
            throw new Svn2RssException("Error parsing xml-based svn log content.\nErrors:\n".implode("\n", $arrParseErrors));

        foreach($objSimpleXmlElement->logentry as $objOneLogEntry) {

            $arrObjAttributes = $objOneLogEntry->attributes();

            $objRssItemNode = $objRssRootNode->addChild("item");

            //title, description, logdate
            $objRssItemNode->addChild("title", $arrObjAttributes->revision->__toString()." by ".$objOneLogEntry->author->__toString());
            $objRssItemNode->addChild("description", $objOneLogEntry->msg->__toString());
            $objRssItemNode->addChild("pubDate", $objOneLogEntry->date->__toString());
            
            $arrRssNodeList[] = $objRssItemNode;
        }


        return $arrRssNodeList;
    }


    
}
?>
