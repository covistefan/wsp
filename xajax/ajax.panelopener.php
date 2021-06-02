<?php
/**
 * ...
 * @author stefan@covi.de
 * @since 6.0
 * @version 7.0
 * @lastchange 2016-09-12
 */
session_start();
if (isset($_REQUEST['panel'])):
	if (isset($_REQUEST['stat']) && intval($_REQUEST['stat'])>0):
		$_SESSION['wspvars']['panelstat'][$_REQUEST['panel']] = 1;
	else:
		$_SESSION['wspvars']['panelstat'][$_REQUEST['panel']] = 0;
	endif;
endif;
?>
