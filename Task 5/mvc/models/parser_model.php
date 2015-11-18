<?php
/**
 * User: Milos Savic
 */

class Model {
    protected $address;
    protected $item;

    // constructor
    public function __construct($address, $item)
    {
        $this->address = $address;
        $this->item = $item;
    }

    /**
     * traverses down the DOM tree to find descendants of an element
     * @return string $nodes
     */
    public function nodes()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false; // we don't want to bother with white spaces
        $dom->formatOutput = true;
        @$dom->loadHTMLFile($this->address); // load given address

        // create DOMXPath from DOMDocument
        $xpath = new DOMXPath($dom);

        $expression = $this->XPath();

        // evaluates the given XPath expression
        $nodeList = $xpath->query($expression);

        $nodes = array();
        // adds the nodes to an array
        foreach ($nodeList as $node)
        {
            array_push($nodes, $this->DOMinnerHTML($node));
        }

        return $nodes;
    }

    /**
     * traverses down the DOM tree to find links
     * @return string $links
     */
    public function links()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false; // we don't want to bother with white spaces
        $dom->formatOutput = true;
        @$dom->loadHTMLFile($this->address); // load given address

        // create DOMXPath from DOMDocument
        $xpath = new DOMXPath($dom);

        $expression = $this->XPath();

        // evaluates the given XPath expression
        $nodeList = $xpath->query($expression);

        $links = array();
        foreach ($nodeList as $node)
        {
            foreach($node->getElementsByTagName('a') as $url)
                array_push($links, $url->getAttribute('href'));
        }

        return $links;
    }

    /**
     * finds the XPath for given item
     * @return string $expression
     */
    protected function XPath()
    {
        $item = $this->item;

        // explode item by one or more spaces
        $items_array = preg_split('/\s+/', $item);

        $expression = 'descendant-or-self::';
        foreach ($items_array as $item) {
            // find # or . (excluding \.) in every item and store its indexes in an array $indexes
            preg_match_all('/(?<!\\\\)[.]|[#]/i', $item, $matches, PREG_OFFSET_CAPTURE);
            $indexes = array();
            foreach($matches[0] as $match)
                array_push($indexes, $match[1]);

            $exploded = array();
            $i = 0;

            // gets the first sub-item if it's not an id or a class
            if (!empty($indexes) && $indexes[0] > 0)
                array_push($exploded, substr($item, 0, $indexes[0]));
            elseif (empty($indexes))
                array_push($exploded, $item);

            if ($item[0] != '#' && $item[0] != '.')
                $expression = rtrim($expression, "*");

            if (!empty($indexes) && $indexes[0] == 0 && $item === reset($items_array))
                $expression .= '*';

            // split item by # and . and insert new items in $exploded
            // eg. item.item1#item2 will be convert to an array with values (item, .item1, #item2)
            foreach ($matches[0] as $match) {
                if ($i+1 < count($indexes))
                    $sub_item = substr($item, $indexes[$i], $indexes[$i+1]-$indexes[$i]);
                else
                    $sub_item = substr($item, $indexes[$i]);

                $i++;
                array_push($exploded, $sub_item);
            }
            //print("<pre>" . print_r($exploded, true) . "</pre>");

            // generate XPath axes - an axis defines a node-set relative to the current node
            foreach ($exploded as $it) {

                if ($it[0] == '#') {
                    $it = str_replace("\\", '', ltrim ($it,'#'));
                    $expression .= "[@id and contains(concat(' ', normalize-space(@id), ' '), '".' '.$it.' '."')]";
                }
                elseif ($it[0] == '.') {
                    $it = str_replace("\\", '', ltrim ($it,'.'));
                    $expression .= "[@class and contains(concat(' ', normalize-space(@class), ' '), '".' '.$it.' '."')]";
                }
                else
                    $expression .= $it;
            }

            // preparing for the descendant if any
            if ($item != end($items_array))
                $expression .= '//*';
        }

        return $expression;
    }

    /**
     * generates inner HTML of an element
     * @param DOMNode $element
     * @return string $innerHTML
     */
    protected function DOMinnerHTML($element)
    {
        $innerHTML = "";
        $children  = $element->childNodes;

        foreach ($children as $child)
        {
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }

        return $innerHTML;
    }

}

// OUTER HTML
//foreach ($nodeList as $node)
//{
//    echo $dom->saveHTML($node), PHP_EOL;
//}
//echo $domTables->ownerDocument->saveHTML($domTables);