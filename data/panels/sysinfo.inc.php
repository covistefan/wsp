<?php

$fh = @fopen('http://'.WSP_UPDSRV."/download/version.php?key=".WSP_UPDKEY, 'r');
$updversion = false;
if (intval($fh)!=0):
    while (!feof($fh)):
        $updversion .= fgets($fh, 4096);
    endwhile;
    fclose($fh);
endif;

if (!($updversion)):
?>
<div class="col-md-3">
    <div class="widget widget-metric_6">
        <span class="icon-wrapper custom-bg-red"><i class="fa fa-warning"></i></span>
        <div class="right">
            <span class="value"><?php echo returnIntLang('home widget sys no connect'); ?></span>
            <span class="title"><?php echo returnIntLang('home widget sys info'); ?></span>
        </div>
    </div>
</div>
<?php
elseif (compareVersion($_SESSION['wspvars']['localversion'],$updversion)>0):
?>
<div class="col-md-3">
    <div class="widget widget-metric_6">
        <span class="icon-wrapper custom-bg-red"><i class="fa fa-cloud-download-alt"></i></span>
        <div class="right">
            <span class="value"><?php echo $updversion; ?>/<small><?php echo $_SESSION['wspvars']['localversion']; ?></small></span>
            <span class="title"><?php echo returnIntLang('home widget sys info'); ?></span>
        </div>
    </div>
</div>
<?php
else:
?>
<div class="col-md-3">
    <div class="widget widget-metric_6">
        <span class="icon-wrapper custom-bg-green"><i class="fa fa-cloud"></i></span>
        <div class="right">
            <span class="value"><?php echo $_SESSION['wspvars']['localversion']; ?></span>
            <span class="title"><?php echo returnIntLang('home widget sys info'); ?></span>
        </div>
    </div>
</div>
<?php endif; ?>