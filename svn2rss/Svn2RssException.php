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
 * Wrapper class for exceptions thrown by svn2rss
 *
 * @author Stefan Idler, sidler@mulchprod.de
 */
class Svn2RssException extends Exception {
    
    /**
     * Creates a new exception with the error specified
     * @param string $strError
     */
    public function __construct($strError) {
        parent::__construct($strError);
    }
}
