<?php
/**
 * User: Milos Savic
 */

/**
 * view for link list i.e. their href attributes
 * @return string $nodes
 */
function viewLinks($parser_links)
{
    $links = '';
    if (!empty($parser_links)) {

        $links .= '<ul class="links">';

        foreach ($parser_links as $link) {
            $links .= '<li>' . $link . '</li>';
        }

        $links .= '</ul>';
    }

    return $links;
}