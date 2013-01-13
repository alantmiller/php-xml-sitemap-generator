<?php 

class Shared_Xml_Sitemap_Config
{
    private $_domain;
    private $_path;
    private $_filename;
    private $_entries = array();
    private $_type;
    
    public function __construct($type = 'screen') 
    {
        $this->_type = $type;
    }

    public function get($arg)
    {
        switch ($arg) {
            case 'domain':
                return $this->_domain;
            case 'path':
                return $this->_path;
            case 'filename':
                return $this->_filename;
            case 'filepath':
                return $this->_getFilepath();
            case 'entries':
                return $this->_entries;
        }
    }

    public function setDomain($domain)
    {
        $this->_domain = trim($domain);
        return $this;
    }

    public function setPath($path)
    {
        $path = trim($path);
        // clean trailing slash if exists
        if (substr($path,-1) == '/') {
            $path = substr($path, 0, -1);
        }

        // check if write directory is valid
        if (!is_dir($path)) {
            exit(sprintf('write directory does not exist: %s'."\n",$path));
        }

        // check if write dir is writable
        if (!is_writable($path)) {
            exit(sprintf('write directory not writable: %s'."\n", $path));
        }

        $this->_path = $path;
        return $this;
    }

    public function setFilename($filename)
    {
        $filename = trim($filename);
        if (strtolower(substr($filename,-3)) != 'xml') {
            exit(sprintf('filename must end with: xml: %s'."\n", $filename));
        }

        // remove leading slash if exists
        if (substr($filename, 0, 1) == '/') {
            $filename = substr($filename, 1, 0);
        }

        $this->_filename = $filename;
        return $this;
    }

    public function setEntries($entries)
    {
        if (!is_array($entries)) {
            throw new exception('setEntries() method expecs an array of objects');
        }

        foreach ($entries AS $entry) {
            if (!is_object($entry) || !get_class($entry) == 'xml_sitemap_entry') {
                throw new exception('setEntries() method expects an aray of Shared_Xml_Sitemap_Entry objects');
            }
        }
        $this->_entries = $entries;
        return $this;
    }

    public function sanityCheck()
    {
        if ($this->_type == 'file' && !strlen($this->_filename) > 0) {
            exit('Error: sitemap filename not set in configuration object');
        }

        if (!strlen($this->_domain) > 0) {
            exit('Error: domain not set in configuration object');
        }
    }

    private function _getFilepath()
    {
        return sprintf('%s/%s',$this->_path, $this->_filename);
    }
}