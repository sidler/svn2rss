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
 * This class handles all svn-related stuff such as connecting to the svn-server, fetching the logs, ....
 * In addition, it implements a caching in order to avoid uneccessary network-requests
 *
 * @author Stefan Idler, sidler@mulchprod.de
 */
class SvnReader {
    
    /**
     *
     * @var ConfigReader
     */
    private $objConfig;

    public function __construct(ConfigReader $objConfig) {
        $this->objConfig = $objConfig;
    }

    public function getSvnLogContent() {
       $strLogContent = false;
       
       //anything to load via the cache?
       if($this->objConfig->getBitCachingEnabled()) {
           $strLogContent = $this->getContentFromCache();
       }

       if($strLogContent === false) {
           //load the logfile via a system-call
           $strLogContent = $this->loadLoghistoryViaSvn();
       }

       //write back to the cache
       if($this->objConfig->getBitCachingEnabled()) {
           $this->setContentToCache($strLogContent);
       }

       return $strLogContent;
    }

    
    private function loadLoghistoryViaSvn() {
        $intExitCode = 0;
        $arrSvnLogLines = array();

        //build the command
        $arrCommand = array();
        $arrCommand[] = escapeshellcmd($this->objConfig->getStrSvnBinaryPath());
        $arrCommand[] = "log -v --xml --no-auth-cache";
        $arrCommand[] = escapeshellarg($this->objConfig->getStrSvnUrl());
        $arrCommand[] = " -l ".escapeshellarg($this->objConfig->getIntLogAmount());
        
        if($this->objConfig->getStrSvnUsername() != "" )
            $arrCommand[] = "--username ".escapeshellarg($this->objConfig->getStrSvnUsername());

        if($this->objConfig->getStrSvnPassword() != "" )
            $arrCommand[] = "--password ".escapeshellarg($this->objConfig->getStrSvnPassword());


        exec(implode(" ", $arrCommand), $arrSvnLogLines, $intExitCode);


        if($intExitCode == 0) {
            return implode("\n", $arrSvnLogLines);
        }

        throw new Svn2RssException("Error loading svn-log content, exit code: ".$intExitCode);
    }

    /**
     * Tries to load the logfile from a cached request.
     *
     * @return string cachecontent, false if cache is invalid or no cached log was found
     */
    private function getContentFromCache() {
        //search for a cached file
        $strFilename = $this->generateCachename();
        if(is_file(SVN2RSS_PROJECT_ROOT."/".SVN2RSS_SYSTEM_FOLDER."/".$strFilename)) {
            //file exists, read content
            $strFileContent = file_get_contents(SVN2RSS_PROJECT_ROOT."/".SVN2RSS_SYSTEM_FOLDER."/".$strFilename);

            //validate cache-lease-time, so get first row
            $strFirstRow = trim( substr($strFileContent, 0, strpos($strFileContent, "\r\n")) );

            //format: projectname | version | generation time hr | gentime in secs
            $arrFirstRowParts = explode("|", $strFirstRow);
            $intTimestamp = trim($arrFirstRowParts[3]);

            //validate the timestamp against the refreh interval
            if($intTimestamp >= (time() - $this->objConfig->getIntRefreshInterval()) ) {
                $strLogContent = substr($strFileContent, strpos($strFileContent, "\r\n")+2 );
                return $strLogContent;
            }
        }
        return false;
    }

    /**
     * Saves the passed string to a cache-file
     *
     * @param string $strContent
     * @return boolean
     */
    private function setContentToCache($strContent) {

        //update the log-content
        $strHeaderRow = "svn2rss | ".SVN2RSS_VERSION." | ".strftime("%a, %d %b %Y %H:%M:%S GMT", time())." | ".time()."\r\n";
        $strContent = $strHeaderRow.$strContent;

        if(file_put_contents(SVN2RSS_PROJECT_ROOT."/".SVN2RSS_SYSTEM_FOLDER."/".$this->generateCachename(), $strContent) !== false )
            return true;


        return false;
    }


    private function generateCachename() {
        return md5($this->objConfig->getStrSvnUrl().$this->objConfig->getStrConfigSetName()).".log";
    }

}
?>
