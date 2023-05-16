<?php

namespace App\Models;

class Overtime_model extends Crud_model {

    protected $table = null;
    protected $primaryKey='uuid';
    protected $useSoftDeletes=true;
    protected $useTimestamps=false;
    protected $dateformat='date_time';
    protected $deletedField='deleted_at';

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

    function delete_overtime($id = 0, $undo = false) {
        try {
            validate_numeric_value($id);
        $data = array('deleted_at' => 'NOW()');
        if ($undo === true) {
            $data = array('deleted_at' => null);
        }
        $this->db_builder->where("uuid", $id);
        $success = $this->db_builder->update($data);
        if ($success) {
            if ($this->log_activity) {
                if ($undo) {
                    // remove previous deleted log.
                    $this->Activity_logs_model->delete_where(array("action" => "deleted", "log_type" => $this->log_type, "log_type_id" => $id));
                } else {
                    //to log this activity check the title
                    $model_info = $this->get_one_uuid($id);
                    $log_for_id = 0;
                    if ($this->log_for_key) {
                        $log_for_key = $this->log_for_key;
                        $log_for_id = $model_info->$log_for_key;
                    }
                    $log_type_title_key = $this->log_type_title_key;
                    $log_type_title = $model_info->$log_type_title_key;
                    $log_data = array(
                        "action" => "deleted",
                        "log_type" => $this->log_type,
                        "log_type_title" => $log_type_title ? $log_type_title : "",
                        "log_type_id" => $id,
                        "log_for" => $this->log_for,
                        "log_for_id" => $log_for_id,
                    );
                    $this->Activity_logs_model->ci_save($log_data);
                }
            }
        }

        try {
            app_hooks()->do_action("app_hook_data_delete", array(
                "id" => $id,
                "table" => $this->table
            ));
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
        }

        return $success;
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }
    function get_maxid() {
        $overtime_table = $this->db->prefixTable('overtime');
        
        $sql="SELECT MAX(CAST($overtime_table.uuid AS INT)+1) AS uuid
        FROM $overtime_table";
        return $this->db->query($sql);

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
        WHERE $overtime_table.deleted_at IS NULL $where
        ORDER BY CAST($overtime_table.uuid AS INT)";
        return $this->db->query($sql);
    }

    function get_summary($options = array()) {
        $overtime_table = $this->db->prefixTable('overtime');
        $users_table = $this->db->prefixTable('users');
        $ovt_type_table = $this->db->prefixTable('ovt_type');

        $where = "";

        $where .= " AND $overtime_table.ovt_status>5 AND MOD($overtime_table.ovt_status,2)=1";


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
            WHERE $overtime_table.deleted_at is null $where
            GROUP BY $overtime_table.employee_id, $overtime_table.ovt_type_id";
        return $this->db->query($sql);
    }

    function get_one_uuid($id = 0) {
        return $this->get_one_where(array('uuid' => $id));
    }

    function ci_save_overtime(&$data = array(), $id = 0) {
        try {
            //allowed fields should be assigned
        $db_fields = $this->db->getFieldNames($this->table);
        foreach ($db_fields as $field) {
            if ($field !== "uuid") {
                array_push($this->allowedFields, $field);
            }
        }

        //unset custom created by field if it's defined for activity log
        $activity_log_created_by_app = false;
        if (get_array_value($data, "activity_log_created_by_app")) {
            $activity_log_created_by_app = true;
            unset($data["activity_log_created_by_app"]);
        }

        if ($id) {
            $id = $this->db->escapeString($id);

            //update
            $where = array("uuid" => $id);

            //to log an activity we have to know the changes. now collect the data before update anything
            if ($this->log_activity) {
                $data_before_update = $this->get_one_uuid($id);
            }

            $success = $this->update_where($data, $where);
            if ($success) {
                if ($this->log_activity) {
                    //unset status_changed_at field for task update
                    if ($this->log_type === "task" && isset($data["status_changed_at"])) {
                        unset($data["status_changed_at"]);
                    }

                    //to log this activity, check the changes
                    $fields_changed = array();
                    foreach ($data as $field => $value) {
                        if ($data_before_update->$field != $value) {
                            $fields_changed[$field] = array("from" => $data_before_update->$field, "to" => $value);
                        }
                    }
                    //has changes? log the changes.
                    if (count($fields_changed)) {
                        $log_for_id = 0;
                        if ($this->log_for_key) {
                            $log_for_key = $this->log_for_key;
                            $log_for_id = $data_before_update->$log_for_key;
                        }

                        $log_for_id2 = 0;
                        if ($this->log_for_key2) {
                            $log_for_key2 = $this->log_for_key2;
                            $log_for_id2 = $data_before_update->$log_for_key2;
                        }

                        $log_type_title_key = $this->log_type_title_key;
                        $log_type_title = isset($data_before_update->$log_type_title_key) ? $data_before_update->$log_type_title_key : "";

                        $log_data = array(
                            "action" => "updated",
                            "log_type" => $this->log_type,
                            "log_type_title" => $log_type_title,
                            "log_type_id" => $id,
                            "changes" => serialize($fields_changed),
                            "log_for" => $this->log_for,
                            "log_for_id" => $log_for_id,
                            "log_for2" => $this->log_for2,
                            "log_for_id2" => $log_for_id2,
                        );
                        $this->Activity_logs_model->ci_save($log_data, $activity_log_created_by_app);
                        $activity_log_id = $this->db->insertID();
                        $data["activity_log_id"] = $activity_log_id;
                    }
                }
            }

            try {
                app_hooks()->do_action("app_hook_data_update", array(
                    "id" => $id,
                    "table" => $this->table,
                    "data" => $data
                ));
            } catch (\Exception $ex) {
                log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
            }

            return $success;
        } else {
            //insert
            
            if ($this->db_builder->insert($data)) {
                $insert_id = $this->db->insertID();
                if ($this->log_activity) {
                    //log this activity
                    $log_for_id = 0;
                    if ($this->log_for_key) {
                        $log_for_id = get_array_value($data, $this->log_for_key);
                    }

                    $log_for_id2 = 0;
                    if ($this->log_for_key2) {
                        $log_for_id2 = get_array_value($data, $this->log_for_key2);
                    }

                    $log_type_title = get_array_value($data, $this->log_type_title_key);
                    $log_data = array(
                        "action" => "created",
                        "log_type" => $this->log_type,
                        "log_type_title" => $log_type_title ? $log_type_title : "",
                        "log_type_id" => $insert_id,
                        "log_for" => $this->log_for,
                        "log_for_id" => $log_for_id,
                        "log_for2" => $this->log_for2,
                        "log_for_id2" => $log_for_id2,
                    );
                    $this->Activity_logs_model->ci_save($log_data, $activity_log_created_by_app);
                    $activity_log_id = $this->db->insertID();
                    $data["activity_log_id"] = $activity_log_id;
                }

                try {
                    app_hooks()->do_action("app_hook_data_insert", array(
                        "id" => $insert_id,
                        "table" => $this->table,
                        "data" => $data
                    ));
                } catch (\Exception $ex) {
                    log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
                }

                return $insert_id;
            }
        }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
