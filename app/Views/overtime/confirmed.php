<div class="table-responsive">
    <table id="confirmed-table" class="display" cellspacing="0" width="100%">            
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#confirmed-table").appTable({
            source: '<?php echo_uri("overtime/confirmed_list_data") ?>',
            dateRangeType: "monthly",
            columns: [
                {title: '<?php echo app_lang("uuid") ?>'},
                {title: '<?php echo app_lang("username") ?>', "class": "w20p"},
                {title: '<?php echo app_lang("hours") ?>', "class": "w20p"},
                {title: '<?php echo app_lang("status_overtime") ?>', "class": "w20p"},
                {title: '<?php echo app_lang("tipe_task_overtime") ?>', "class": "w15p"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });
    });
</script>

