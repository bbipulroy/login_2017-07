<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$user=User_helper::get_user();
$sites=User_helper::get_accessed_sites();

?>
<div class="row widget">
    <div style="" class="row show-grid">
        <div class="col-sm-12 text-center">
            <h1><?php echo $user->name;?></h1>
            <img style="max-width: 250px;" src="<?php echo $user->picture_profile; ?>">
        </div>
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
