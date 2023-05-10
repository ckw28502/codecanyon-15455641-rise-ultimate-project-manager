<div class="table-responsive">
    <table id="pending-approval-table" class="display" cellspacing="0" width="100%">            
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#pending-approval-table").appTable({
            source: '<?php echo_uri("leaves/pending_approval_list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("uuid") ?>', "class": "w20p"},
                {title: '<?php echo app_lang("username") ?>'},
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

