<?php

class ExtendedSimpleXMLElement extends SimpleXMLElement
{

    /**
     * Makes nicely formatted XML from the node
     *
     * @param string $filename
     * @param int|boolean $level if false
     * @return string
     */
    public function asNiceXml($filename='', $level=0)
    {
        if (is_numeric($level)) {
            $pad = str_pad('', $level * 3, ' ', STR_PAD_LEFT);
            $nl = "\n";
        } else {
            $pad = '';
            $nl = '';
        }

        $out = $pad . '<' . $this->getName();

        if ($attributes = $this->attributes()) {
            foreach ($attributes as $key => $value) {
                $out .= ' ' . $key . '="' . str_replace('"', '\"', (string) $value) . '"';
            }
        }

        if ($this->hasChildren()) {
            $out .= '>' . $nl;
            foreach ($this->children() as $child) {
                $out .= $child->asNiceXml('', is_numeric($level) ? $level + 1 : true);
            }
            $out .= $pad . '</' . $this->getName() . '>' . $nl;
        } else {
            $value = (string) $this;
            if (strlen($value)) {
                $out .= '>' . $this->xmlentities($value) . '</' . $this->getName() . '>' . $nl;
            } else {
                $out .= '/>' . $nl;
            }
        }

        if ((0 === $level || false === $level) && !empty($filename)) {
            file_put_contents($filename, $out);
        }

        return $out;
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function hasChildren()
    {
        if (!$this->children()) {
            return false;
        }

        // simplexml bug: @attributes is in children() but invisible in foreach
        foreach ($this->children() as $k=>$child) {
            return true;
        }
        return false;
    }

    /**
     * Converts meaningful xml characters to xml entities
     *
     * @param  string
     * @return string
     */
    public function xmlentities($value = null)
    {
        if (is_null($value)) {
            $value = $this;
        }
        $value = (string)$value;

        $value = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $value);

        return $value;
    }
    
}
