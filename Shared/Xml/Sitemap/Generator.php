<?php
/**
* @author Alan T. Miller <alan@alanmiller.com>
* @copyright Copyright (C) 2010, Alan T Miller, All Rights Reserved.
 *
 * Creates a properly formatted XML sitemap
 *
 * Sample Usage:
 *
 * // Create an array of xml_sitemap_entry objects
 * $entries[] = new xml_sitemap_entry('/', '1.00', 'weekly');
 * $entries[] = new xml_sitemap_entry('/somepage.html', '0.95', 'weekly');
 * $entries[] = new xml_sitemap_entry('/otherpage.html', '0.90', 'monthly');
 *
 * // set up the xml generator config object
 * $conf = new xml_sitemap_generator_config();
 * $conf->setDomain('www.somedomainsomewhere.com');
 * $conf->setPath('/var/tmp/');
 * $conf->setFilename('sitemap.xml');
 * $conf->setEntries($entries);
 *
 * // instantiate and execute
 * $generator = new xml_sitemap_generator($conf);
 * $generator->write(); // or $generator->toString();
 *
 */
class Shared_Xml_Sitemap_Generator
{
    private $_conf;
    private $_blocks;
    private $_xml;

    public function __construct(Shared_Xml_Sitemap_Config $conf)
    {
        $this->_conf = $conf;
    }

    /**
     * exists to maintain legacy interface,
     * other than that, it is not needed.
     *
     */
    public function build()
    {
        $this->_build();
    }

    /**
     * retrieve XML sitemap as a string
     *
     * @return unknown
     */
    public function getXml()
    {
        $this->_build();
        return $this->_xml;
    }

    public function toString()
    {
        $this->_build();
        return $this->_xml;
    }

    /**
     * write XML sitemap to disk
     *
     */
    public function write()
    {
        $this->_build();

        print_r($this->_xml);
        if (!file_put_contents($this->_conf->get('filepath'), $this->_xml)){
            throw new Exception('cound not write file: '.$this->_conf->get('filepath')."\n");
        }
    }

    private function _append($xml)
    {
        $this->_xml .= $xml;
    }

    private function _build()
    {
        $this->_conf->sanityCheck();
        $this->_append($this->_buildHeader());
        $this->_append($this->_buildBlocks());
        $this->_append($this->_buildFooter());
    }

    private function _buildHeader()
    {
        $header  = '<'.'?'.'xml version="1.0" encoding="UTF-8"?'.'>'."\n";
        $header .= "\t".'<urlset ';
        $header .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        return $header;
    }

    private function _buildFooter()
    {
        return '</urlset>'."\n";
    }

    private function _buildBlocks()
    {
        foreach ($this->_conf->get('entries') AS $entry) {
            $this->_blocks .= $this->_buildEntry($entry);
        }
        return $this->_blocks;
    }

    private function _buildEntry(Shared_Xml_Sitemap_Entry $entry)
    {
        $loc = sprintf("http://%s%s",
                       $this->_conf->get('domain'),$entry->get('loc'));

        return sprintf("<url>\n%s%s%s%s</url>\n",
                $this->_buildLine('loc', $loc),
                $this->_buildLine('priority',$entry->get('priority')),
                $this->_buildLine('changefreq',$entry->get('changefreq')),
                $this->_buildLine('lastmod', $entry->get('lastmod')));
    }

    private function _buildLine($tagname, $content)
    {
        if(!$this->_is_utf8($content)) {
            $content = trim(utf8_encode($content));
        }
        return sprintf("\t<%s>%s</%s>\n",
                       $tagname, $content, $tagname);
    }

    private function _is_utf8($str)
    {
        // function borrowed from:
        // http://w3.org/International/questions/qa-forms-utf-8.html

        return preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs', $str);
    }


}