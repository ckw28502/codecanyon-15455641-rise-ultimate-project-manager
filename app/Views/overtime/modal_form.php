<?php echo form_open(get_uri("overtime/" . $form_type), array("id" => "overtime-form", "class" => "general-form", "role" => "form")); ?>
<div id="leaves-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            

            <div class="form-group">
                <div class="row">
                    <label for="employee" class=" col-md-3"><?php echo app_lang('employee'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("employee_id", $employee, "", "class='select2 validate-hidden' id='employee_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>
            <div class=" form-group">
                <div class="row">
                    <label for="duration" class=" col-md-3"><?php echo app_lang('duration'); ?></label>
                    <div class=" col-md-9">
                    <input type="number" id="duration" name="duration" min="1" max="999">
                    </div>
                    
                    </form>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="overtime_type" class=" col-md-3"><?php echo app_lang('overtime_type'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("overtime_type_id", $overtimetype, "", "class='select2 validate-hidden' id='overtime_type_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>





            <?php echo view("includes/dropzone_preview"); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-default upload-file-button float-start me-auto btn-sm round" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_file"); ?></button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang($form_type); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var uploadUrl = "<?php echo get_uri("leaves/upload_file"); ?>";
        var validationUri = "<?php echo get_uri("leaves/validate_leaves_file"); ?>";

        var dropzone = attachDropzoneWithForm("#leaves-dropzone", uploadUrl, validationUri);

        $("#overtime-form").appForm({
            onSuccess: function (result) {
                location.reload();
            }
        });

        setDatePicker("#start_date, #end_date");

        setDatePicker("#single_date, #hour_date");


        $("#overtime-form .select2").select2();

        $(".duration").click(function () {
            var value = $(this).val();
            $(".date_section").addClass("hide");
            if (value === "multiple_days") {
                $("#multiple_days_section").removeClass("hide");
            } else if (value === "hours") {
                $("#hours_section").removeClass("hide");
            } else {
                $("#single_day_section").removeClass("hide");
            }
        });


        $("#multiple_days_section").change(function () {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            if (start_date && end_date) {
                $("#total_days_section").removeClass("hide");

                var start_date = moment($('#start_date').val(), getJsDateFormat().toUpperCase());
                var end_date = moment($('#end_date').val(), getJsDateFormat().toUpperCase());
                var total_days = end_date.diff(start_date, 'days');

                $('div.total-days').html((total_days * 1) + 1); //count the starting day too
            } else {
                $("#total_days_section").addClass("hide");
            }
        });

    });
</script>