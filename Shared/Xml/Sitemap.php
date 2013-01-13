<?php 
class Shared_Xml_Sitemap 
{    
    protected $conf;
    protected $entries = array();
    
    public function __construct() 
    {
        $this->conf = new Shared_Xml_Sitemap_Config();   
    }
    
    public function addEntry($loc, $priority, $changefreq="", $lastmod='') 
    {
        $this->entries[] = new Shared_Xml_Sitemap_Entry($loc, $priority, $changefreq, $lastmod);   
    }
    
    public function setDomain($domain) 
    {
        $this->conf->setDomain($domain); 
    }
    
    public function toString() 
    {
        $this->conf->setEntries($this->entries);
        $generator = new Shared_Xml_Sitemap_Generator($this->conf);
        return $generator->toString();
    }
}