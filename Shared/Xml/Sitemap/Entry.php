<?php 

class Shared_Xml_Sitemap_Entry
{
    private $_loc;
    private $_priority;
    private $_changefreq;
    private $_lastmod;

    private $_frequencies = array('always','hourly','daily','weekly','monthly','yearly','never');
    private $_priorities = array('0.0','0.1','0.2','0.3','0.4','0.5','0.6','0.7','0.8','0.9','1.0');

    public function __construct($loc, $priority, $changefreq="", $lastmod='')
    {
        $this->_setLoc($loc);
        $this->_setPriority($priority);

        if (strlen($changefreq)> 0) {
            $this->_setChangefreq($changefreq);
        }
        if (strlen($lastmod)> 0) {
            $this->_setLastmod($lastmod);
        }
    }

    public function get($arg)
    {
        switch($arg)
        {
            case 'loc':
                return $this->_getLoc();
            case 'priority':
                return $this->_getPriority();
            case 'changefreq':
                return $this->_getChangefreq();
            case 'lastmod':
                return $this->_getLastmod();
            case 'frequencies':
                return $this->_frequencies;
            case 'priorities':
                return $this->_priorities;
            default:
                throw new Exception('get() method in class: '.__CLASS__.' did not recognize the argument');
        }
    }

    private function _setLoc($loc)
    {
        $this->_loc = $loc;
        return $this;
    }

    private function _setPriority($priority)
    {
        $priority = trim($priority);
        
        if (!in_array($priority, $this->_priorities)) {
            throw new Exception('setPriority() method is expecting a value between 0.0 and 1.0');
        } else {
            $this->_priority = $priority;
        }
        return $this;
    }

    private function _setChangefreq($changefreq)
    {
        $changefreq = strtolower(trim($changefreq));

        if (!in_array($changefreq, $this->_frequencies)) {
            throw new Exception('setChangefreq() method is expecting a value such as hourly daily, weekly etc');
        } else {
            $this->_changefreq = $changefreq;
        }
        return $this;
    }

    private function _setLastmod($lastmod)
    {
        $arr = date_parse($lastmod);
        if (!checkdate($arr['month'], $arr['day'], $arr['year'])) {
            throw new Exception('setLastmod() method expects a valid date');
        } else {
            $arr['month'] = str_pad($arr['month'], 2, "0", STR_PAD_LEFT);
            $arr['day'] = str_pad($arr['day'], 2, "0", STR_PAD_LEFT);
            $this->_lastmod =
                sprintf('%s-%s-%s',$arr['year'], $arr['month'], $arr['day']);
        }
        return $this;
    }

    private function _getLoc()
    {
        return $this->_loc;
    }

    private function _getPriority()
    {
        if (!strlen($this->_priority) > 0) {
            return  '0.5';
        } else {
            return $this->_priority;
        }
    }

    private function _getLastmod()
    {
        // set default values
        if (!strlen($this->_lastmod) > 0) {
             return date('Y-m-d');
        } else {
            return $this->_lastmod;
        }
    }

    private function _getChangefreq()
    {
        if (!strlen($this->_changefreq) > 0) {
            return 'monthly';
        } else {
            return $this->_changefreq;
        }
    }
}

