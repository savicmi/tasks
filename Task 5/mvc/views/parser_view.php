<?php
/**
 * User: Milos Savic
 */

/**
 * view for inner HTML of the nodes
 * @return string $nodes
 */
function viewNodes($parser_nodes)
{
    $nodes = '';
    foreach ($parser_nodes as $node) {
        $nodes .= '<div class="node">' . $node . '</div>';
    }

    return $nodes;
}