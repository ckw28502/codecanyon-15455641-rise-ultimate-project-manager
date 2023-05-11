<div class="table-responsive">
    <table id="overtime-summary-table" class="display" cellspacing="0"width="100%">
    </table>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        $("#overtime-summary-table").appTable({
            source: '<?php echo_uri("overtime/summary_list_data") ?>',
            filterDropdown: [
                {name: "overtime_type_id", class: "w200", options: <?php echo $overtime_types_dropdown; ?>},
                {name: "employee_id", class: "w200", options: <?php echo $employees_dropdown; ?>}
            ],
            dateRangeType: "yearly",
            columns: [
                {title: '<?php echo app_lang("employee") ?>', "class": "w30p"},
                {title: '<?php echo app_lang("overtime_type") ?>'},
                {title: '<?php echo app_lang("total_hours_yearly") ?>'}
            ],
            printColumns: [0, 1, 2],
            xlsColumns: [0, 1, 2]
        });
    }
    );
</script>