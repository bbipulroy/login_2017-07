<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
<input type="hidden" id="id" name="id" value="<?php echo $customer['id']; ?>" />
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
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_TYPE');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['type_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INCHARGE');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['incharge_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['division_name'];?></label>
    </div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['zone_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['territory_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['district_name'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['name']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_SHORT_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['name_short']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_CODE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['customer_code']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_CREDIT_LIMIT');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['credit_limit']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME_OWNER');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['name_owner']; ?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TIN');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['tin']; ?></label>
    </div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NID');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['nid'] ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PICTURE');?></label>
    </div>
    <div class="col-xs-4" id="image_profile">
        <img style="max-width: 250px;" src="<?php echo $customer_info['picture_profile']; ?>">
    </div>
    <div class="col-sm-2"></div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME_MARKET');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['name_market'];?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ADDRESS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['address']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MAP_ADDRESS');?></label>
    </div>
    <div class="col-xs-6">
        <?php if($customer_info['map_address']) {echo $customer_info['map_address'];}?>
    </div>
</div>



<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_PHONE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['phone'];?></label>
    </div>
</div>



<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_EMAIL');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['email'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AGREEMENT');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['status_agreement']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label for="opening_date" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_OPENING');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php if($customer_info['opening_date'])  echo System_helper::display_date($customer_info['opening_date']); ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label for="closing_date" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_CLOSING');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php if($customer_info['closing_date']) {echo System_helper::display_date($customer_info['closing_date']);}?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['ordering']?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('STATUS');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer['status']; ?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $customer_info['remarks']; ?></label>
    </div>
</div>

</div>

<div class="clearfix"></div>
</form>
<script type="text/javascript">

</script>
