<?php

namespace App\Models;

class Overtime_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'overtime';
        parent::__construct($this->table);
    }

    function get_details_info($uuid = 0) {
        $overtimes_table = $this->db->prefixTable('overtime');
        $users_table = $this->db->prefixTable('users');
        $ovt_types_table = $this->db->prefixTable('ovt_type');

        $sql = "SELECT $overtimes_table.*, 
                CONCAT($users_table.first_name, ' ',$users_table.last_name) AS username, $users_table.image as employee_avatar, $users_table.job_title,
                t.type_name as tipe_task_overtime, s.type_name as status_overtime
            FROM $overtimes_table
            LEFT JOIN $users_table ON $users_table.id= $overtimes_table.employee_id
            LEFT JOIN $ovt_types_table t ON t.id= $overtimes_table.ovt_type_id        
            LEFT JOIN $ovt_types_table s ON s.id= $overtimes_table.ovt_status        
            WHERE $overtimes_table.deleted_at IS NULL AND $overtimes_table.uuid=$uuid";
        return $this->db->query($sql)->getRow();
    }

    function get_list($options = array()) {
        $overtime_table = $this->db->prefixTable('overtime');
        $users_table = $this->db->prefixTable('users');
        $ovt_type_table = $this->db->prefixTable('ovt_type');
        $ovt_status_table = $this->db->prefixTable('ovt_type');
        $where = "";
        $uuid = $this->_get_clean_value($options, "uuid");
        if ($uuid) {
            $where = " AND $overtime_table.uuid=$uuid";
        }

        $type = $this->_get_clean_value($options, "type");
        if ($type) {
            $where .= " AND t.type_name='$type'";
        }

        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND s.type_name='$status'";
        }

        $start_date = $this->_get_clean_value($options, "start_date");
        $end_date = $this->_get_clean_value($options, "end_date");

        if ($start_date && $end_date) {
            $where .= " AND ($overtime_table.overtime_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $employee_id = $this->_get_clean_value($options, "employee_id");
        if ($employee_id) {
            $where .= " AND $overtime_table.employee_id=$employee_id";
        }

        $type=$this->_get_clean_value($options,"type");
        if ($type) {
            $where .=" AND $ovt_type_table.type_name=$type";
        }

        $not_status=$this->_get_clean_value($options,"not_status");
        if ($not_status) {
            $where .=" AND s.type_name!='$not_status'";
        }
        $access_type = $this->_get_clean_value($options, "access_type");

        if (!$uuid && $access_type !== "all") {

            $allowed_members = $this->_get_clean_value($options, "allowed_members");
            if (is_array($allowed_members) && count($allowed_members)) {
                $allowed_members = join(",", $allowed_members);
            } else {
                $allowed_members = '0';
            }
            $login_user_id = $this->_get_clean_value($options, "login_user_id");
            if ($login_user_id) {
                $allowed_members .= "," . $login_user_id;
            }
            $where .= " AND $overtime_table.employee_id IN($allowed_members)";
        }


        $sql="SELECT $overtime_table.uuid , CONCAT($users_table.first_name,' ',$users_table.last_name) AS username, $overtime_table.hours,t.type_name AS tipe_task_overtime, s.type_name AS status_overtime, $users_table.image AS employee_avatar,$users_table.id AS employee_id
        FROM $overtime_table
        LEFT JOIN $users_table ON $users_table.id=$overtime_table.employee_id
        LEFT JOIN $ovt_type_table t ON t.id=$overtime_table.ovt_type_id
        LEFT JOIN $ovt_status_table s ON s.id=$overtime_table.ovt_status
        WHERE $overtime_table.deleted_at IS NULL $where";
        //$sql="SELECT $overtime_table.* from $overtime_table WHERE $overtime_table.deleted_at IS NULL";
        return $this->db->query($sql);
    }

    function get_summary($options = array()) {
        $overtime_table = $this->db->prefixTable('overtime');
        $users_table = $this->db->prefixTable('users');
        $ovt_type_table = $this->db->prefixTable('ovt_type');

        $where = "";

        $where .= " AND $ovt_type_table.type_name='Approved'";


        $start_date = $this->_get_clean_value($options, "start_date");
        $end_date = $this->_get_clean_value($options, "end_date");

        if ($start_date && $end_date) {
            $where .= " AND ($overtime_table.overtime_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $employee_id = $this->_get_clean_value($options, "employee_id");
        if ($employee_id) {
            $where .= " AND $overtime_table.employee_id=$employee_id";
        }

        $ovt_type_id = $this->_get_clean_value($options, "ovt_type_id");
        if ($ovt_type_id) {
            $where .= " AND $overtime_table.ovt_type_id=$ovt_type_id";
        }

        $access_type = $this->_get_clean_value($options, "access_type");

        if ($access_type !== "all") {

            $allowed_members = $this->_get_clean_value($options, "allowed_members");
            if (is_array($allowed_members) && count($allowed_members)) {
                $allowed_members = join(",", $allowed_members);
            } else {
                $allowed_members = '0';
            }
            $login_user_id = $this->_get_clean_value($options, "login_user_id");
            if ($login_user_id) {
                $allowed_members .= "," . $login_user_id;
            }
            $where .= " AND $overtime_table.employee_id IN($allowed_members)";
        }


        $sql = "SELECT  SUM($overtime_table.hours) as hours, MAX($overtime_table.employee_id) as employee_id, $overtime_table.ovt_status as status,
                CONCAT($users_table.first_name, ' ',$users_table.last_name) AS username, $users_table.image as employee_avatar,
                $ovt_type_table.type_name as tipe_task_overtime
            FROM $overtime_table
            LEFT JOIN $users_table ON $users_table.id= $overtime_table.employee_id
            LEFT JOIN $ovt_type_table ON $ovt_type_table.id= $overtime_table.ovt_type_id        
            WHERE $overtime_table.deleted_at is null
            GROUP BY $overtime_table.employee_id, $overtime_table.ovt_type_id";
        return $this->db->query($sql);
    }

    function get_one_uuid($id = 0) {
        return $this->get_one_where(array('uuid' => $id));
    }

}
