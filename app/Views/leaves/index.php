<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card">
        <div class="page-title clearfix leaves-page-title">
            <h1><?php echo app_lang('overtimes'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("leaves/apply_leave_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('create_overtime'), array("class" => "btn btn-default", "title" => app_lang('apply_leave'))); ?>
            </div>
        </div>
        <ul id="leaves-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white inner" role="tablist">
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/pending_approval/"); ?>" data-bs-target="#leave-pending-approval"><?php echo app_lang("waiting_acknowledge"); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/all_applications/"); ?>" data-bs-target="#leave-all-applications"><?php echo app_lang("confirmed"); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/summary/"); ?>" data-bs-target="#leave-summary"><?php echo app_lang("summary"); ?></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade active" id="leave-pending-approval"></div>
            <div role="tabpanel" class="tab-pane fade" id="leave-all-applications"></div>
            <div role="tabpanel" class="tab-pane fade" id="leave-summary"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "all_applications") {
                $("[data-bs-target='#leave-all-applications']").trigger("click");
            }
        }, 210);
    });
</script>