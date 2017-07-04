<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
    'id'=>'button_action_save_new',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $user['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="employee_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user[employee_id]" id="employee_id" class="form-control" value="<?php echo $user['employee_id']; ?>">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="user_name" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_USERNAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user[user_name]" id="user_name" class="form-control" value="<?php echo $user['user_name']; ?>">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="password" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PASSWORD');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user[password]" id="password" class="form-control" value="">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="name" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user_info[name]" id="name" class="form-control" value="<?php echo $user_info['name'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPANY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                    foreach($companies as $company)
                    {
                        ?>
                        <div class="checkbox">
                            <label title="<?php echo $company['full_name']; ?>">
                                <input type="checkbox" name="company[]" value="<?php echo $company['id']; ?>"><?php echo $company['short_name']; ?>
                            </label>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="designation" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DESIGNATION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="designation" name="user_info[designation]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                        foreach($designations as $designation)
                        {
                            ?>
                            <option value="<?php echo $designation['value']; ?>" <?php if($designation['value']==$user_info['designation']){echo 'selected';} ?>><?php echo $designation['text']; ?></option>
                            <?php
                        }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="division_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="division_id" name="area[division_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label for="zone_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="zone_id" name="area[zone_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label for="territory_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="territory_id" name="area[territory_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label for="district_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="district_id" name="area[district_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="upazilla_id_container">
            <div class="col-xs-4">
                <label for="upazilla_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="upazilla_id" name="area[upazilla_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="union_id_container">
            <div class="col-xs-4">
                <label for="union_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UNION_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="union_id" name="area[union_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="ordering" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user_info[ordering]" id="ordering" class="form-control" value="<?php echo $user_info['ordering'] ?>" >
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        $(document).off('change','#designation');
        $(document).off('change','#division_id');
        $(document).off('change','#zone_id');
        $(document).off('change','#territory_id');
        $(document).off('change','#district_id');
        $(document).off('change','#upazilla_id');
        $(document).off('change','#union_id');
        
        $('#division_id').html(get_dropdown_with_select(system_divisions));
        $(document).on("change","#division_id",function()
        {
            $("#zone_id").val("");
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var division_id=$('#division_id').val();
            if(division_id>0)
            {
                $('#zone_id_container').show();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
                $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
            }
            else
            {
                $('#zone_id_container').hide();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
            }
        });
        $(document).on("change","#zone_id",function()
        {
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var zone_id=$('#zone_id').val();
            if(zone_id>0)
            {
                $('#territory_id_container').show();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
                $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
            }
            else
            {
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
            }
        });
        $(document).on("change","#territory_id",function()
        {
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                $('#district_id_container').show();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
                $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
            }
            else
            {
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
            }
        });
        $(document).on("change","#district_id",function()
        {
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var district_id=$("#district_id").val();
            if(district_id>0)
            {
                $('#upazilla_id_container').show();
                $('#union_id_container').hide();
                $.ajax({
                    url: '<?php echo site_url('common_controller/get_dropdown_upazillas_by_districtid'); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{
                        district_id:district_id,
                        html_container_id:'#upazilla_id'
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
            }
        });
        $(document).on("change","#upazilla_id",function()
        {
            $("#union_id").val("");
            var upazilla_id=$("#upazilla_id").val();
            if(upazilla_id>0)
            {
                $('#union_id_container').show();
                $.ajax({
                    url: '<?php echo site_url('common_controller/get_dropdown_unions_by_upazillaid'); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{
                        upazilla_id:upazilla_id,
                        html_container_id:'#union_id'
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#union_id_container').hide();
            }
        });
    });
</script>
