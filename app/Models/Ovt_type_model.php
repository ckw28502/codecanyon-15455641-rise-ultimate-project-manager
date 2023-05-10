<?php

namespace App\Models;

class Ovt_type_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'ovt_type';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $ovt_type_table = $this->db->prefixTable('ovt_type');

        $where = "";
        
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $ovt_type_table.id=$id";
        }

        $sql = "SELECT $ovt_type_table.*
        FROM $ovt_type_table
        WHERE $ovt_type_table.deleted_at is null $where";
        return $this->db->query($sql);
    }

    function get_type() {
        $ovt_type_table = $this->db->prefixTable('ovt_type');
        // $where = 2;

        $sql = "SELECT $ovt_type_table.*
        FROM $ovt_type_table";

        return $this->db->query($sql);
    }


}
