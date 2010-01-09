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
 * Class to manage the access to the config-file.
 * Reads the config file and loads the configSet passed via the constructur.
 * If the param is being left empty, the default-config set is being loaded.
 *
 * @author Stefan Idler, sidler@mulchprod.de
 */
class ConfigReader {
    private $strConfigFile = "svn2rss.xml";

    /**
     *
     * @var SimpleXMLElement
     */
    private $objSimpleXml;

    /**
     *
     * @var SimpleXMLElement
     */
    private $objCurrentConfigSetXml = null;

    private $strConfigSetName = "";

    public function __construct($strConfigSet = "") {

        libxml_use_internal_errors();
        $this->objSimpleXml = simplexml_load_file(SVN2RSS_PROJECT_ROOT."/".$this->strConfigFile);
        $arrParseErrors = libxml_get_errors();
        libxml_clear_errors();

        if(count($arrParseErrors) > 0)
            throw new Svn2RssException("Error parsing xml-config-file ".$this->strConfigFile.".\nErrors:\n".implode("\n", $arrParseErrors));

        if($strConfigSet == "")
            $strConfigSet = $this->getStrDefaultConfigSet();

        if($strConfigSet == "")
            throw new Svn2RssException("No default config-set defined in ".$this->strConfigFile);

        //load the config-set requested
        $this->strConfigSetName = $strConfigSet;
        foreach($this->objSimpleXml->configSets->configSet as $objOneConfigSet) {
            $arrAttributes = $objOneConfigSet->attributes();
            if($arrAttributes->id->__toString() == $strConfigSet) {
                $this->objCurrentConfigSetXml = $objOneConfigSet;
            }
        }

        if($this->objCurrentConfigSetXml == null)
            throw new Svn2RssException("Loading of config set ".$strConfigSet." failed.");
    }


    public function getStrSvnBinaryPath() {
        return $this->objSimpleXml->globalConfig->svnBinaryPath->__toString();
    }

    public function getBitCachingEnabled() {
        if($this->objSimpleXml->globalConfig->cachingEnabled->__toString() == "true")
            return true;
        else
            return false;
    }

    public function getStrDefaultConfigSet() {
        return $this->objSimpleXml->globalConfig->defaultConfigSet->__toString();
    }

    public function getStrSvnUrl() {
        return $this->objCurrentConfigSetXml->svnUrl->__toString();
    }

    public function getStrSvnUsername() {
        return $this->objCurrentConfigSetXml->svnUsername->__toString();
    }

    public function getStrSvnPassword() {
        return $this->objCurrentConfigSetXml->svnPassword->__toString();
    }

    public function getIntLogAmount() {
        return (int)$this->objCurrentConfigSetXml->logAmount->__toString();
    }

    public function getIntRefreshInterval() {
        return (int)$this->objCurrentConfigSetXml->refreshInterval->__toString();
    }

    public function getStrConfigSetName() {
        return $this->strConfigSetName;
    }

    
    
}
?>
