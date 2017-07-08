<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$user=User_helper::get_user();
$CI = & get_instance();
$sites=User_helper::get_accessed_sites();
?>
<div class="row widget">
    <?php
    if($user->user_group==0)
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_NOT_ASSIGNED_GROUP');?></h3>

        </div>
    <?php
    }
    ?>
    <?php
    if($CI->is_site_offline())
    {
        ?>
        <div class="col-sm-12 text-center">
            <h3 class="alert alert-warning"><?php echo $CI->lang->line('MSG_SITE_OFFLINE');?></h3>
        </div>
    <?php
    }
    ?>
    <div class="col-sm-12 text-center">
        <h1><?php echo $user->name;?></h1>
        <img style="max-width: 250px;" src="<?php echo $user->picture_profile; ?>">
    </div>
    <?php
        if(sizeof($sites)>0)
        {
            ?>
            <div class="widget-header">
                <div class="title">
                    Other Sites
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
            foreach($sites as $site)
            {
                ?>
                <div style="" class="row show-grid">
                    <div class="col-xs-12">
                        <label class="control-label"><?php echo strtoupper($site['short_name']);?></label>- - -
                        <a class="external" target="_blank" href="<?php echo site_url('other_sites_visit/visit_site/'.$site['id']); ?>">Visit Site</a>
                    </div>
                </div>
                <?php
            }
        }
    ?>

</div>
<div class="clearfix"></div>
