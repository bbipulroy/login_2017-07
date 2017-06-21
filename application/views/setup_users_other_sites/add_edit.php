<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
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
        <?php

        ?>
        <div style="" class="row show-grid">
            <div class="col-xs-12">
                <?php
                    foreach($sites as $site)
                    {
                        ?>
                        <div class="checkbox">
                            <label title="<?php echo $site['full_name']; ?>">
                                <input type="checkbox" name="sites[]" value="<?php echo $site['id']; ?>" <?php if(in_array($site['id'],$assigned_sites)){echo 'checked';} ?>><?php echo $site['short_name']; ?>
                            </label>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();

    });
</script>
