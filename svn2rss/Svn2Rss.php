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
 * The global manager, handles the processing of a single request.
 *
 * @author Stefan Idler, sidler@mulchprod.de
 */
class Svn2Rss {

    private $strOutput = "";

    /**
     * Starts the processing of the current request.
     * Acts like some kind of a main-method, so manages the further control-flow.
     */
    public function processSvn2RssRequest($strFeedParam = "") {

        try {
            //start by loading the config-file
            $objConfig = new ConfigReader($strFeedParam);

            //create the svn-reader and pass control
            $objSvnReader = new SvnReader($objConfig);
            $strSvnLog = $objSvnReader->getSvnLogContent();
            //$this->strOutput .= $strSvnLog;
            
            //create rss-nodes out of the logfile
            $objRssConverter = new Log2RssConverter($objConfig);
            $arrRssItemNodes = $objRssConverter->generateRssNodesFromLogContent($strSvnLog);

            
            

        }
        catch (Svn2RssException $objException) {
            $this->strOutput = "<error><![CDATA[Something bad happened: \n".$objException->getMessage()."]]></error>";
        }
    }


    public function getStrOutput() {
        return $this->strOutput;
    }


}
?>
