<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card">
        <div class="page-title clearfix overtime-page-title">
            <h1><?php echo app_lang('overtimes'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("overtime/create_overtime_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('create_overtime'), array("class" => "btn btn-default", "title" => app_lang('apply_leave'))); ?>
            </div>
        </div>
        <ul id="overtime-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white inner" role="tablist">
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("overtime/waiting_acknowledge/"); ?>" data-bs-target="#overtime-waiting_acknowledge"><?php echo app_lang("waiting_acknowledge"); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("overtime/confirmed/"); ?>" data-bs-target="#overtime-confirmed"><?php echo app_lang("confirmed"); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("overtime/summary/"); ?>" data-bs-target="#overtime-summary"><?php echo app_lang("summary"); ?></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade active" id="overtime-waiting_acknowledge"></div>
            <div role="tabpanel" class="tab-pane fade" id="overtime-confirmed"></div>
            <div role="tabpanel" class="tab-pane fade" id="overtime-summary"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "confirmed") {
                $("[data-bs-target='#overtime-confirmed']").trigger("click");
            }
        }, 210);
        // $.ajax({
        //     type: "post",
        //     data:{
        //         id:3
        //     },
        //     url: '<?php echo_uri("overtime/delete") ?>',
        //     success:function (data) { console.log(JSON.parse(data)); }
        // })
    });
</script>