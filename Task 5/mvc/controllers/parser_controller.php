<?php
/**
 * User: Milos Savic
 */

include_once("/models/parser_model.php");

class Controller {
    protected $model;
    protected $address;
    protected $item;

    // constructor instantiates a model class
    public function __construct($address, $item) {

        $this->address = $address;
        $this->item = $item;
        $this->model = new Model($address, $item);
    }

    // invoke main functions from the parser model
    public function invoke() {

        $parser_nodes = $this->model->nodes();
        $parser_links = $this->model->links();

        $nodes_links = array("nodes"=>$this->returnView($parser_nodes), "links"=>$this->returnView($parser_links, true));
        return $nodes_links;
    }

    // returns a view (inner HTML or links)
    protected function returnView($parser_data, $links = false)
    {
        $view_location = '/views/parser_view.php';
        if ($links) {
            require('/views/parser_links.php');
            return viewLinks($parser_data);
        } else {
            require($view_location);
            return viewNodes($parser_data);
        }
    }
}
