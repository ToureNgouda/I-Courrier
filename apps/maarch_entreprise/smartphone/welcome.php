<?php
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
$core = new core_tools();
$core->load_lang();
$core->load_header();

if($_SESSION['user']['UserId'] == NULL) {
    ?>
    <script type="text/javascript">
        window.top.location.href = "<?php  functions::xecho($_SESSION['config']['businessappurl']);?>index.php?page=hello";
    </script>
    <?php
}
?>
<ul id="home" title="Maarch" selected="true">
    <li><a href="my_baskets.php"><span class="fa fa-inbox"></span><?php echo  _MY_BASKETS;?></a></li>
    <li><a href="search.php"><span class="fa fa-search"></span><?php echo _SEARCH;?></a></li>
	<!--<li><a href="settings.php"><img src="<?php echo $_SESSION['config']['businessappurl'];?>static.php?filename=picto_menu_admin.gif" alt=""/>&nbsp;&nbsp;Settings</a></li>-->
	<li><a href="my_colleagues.php"><span class="fa fa-users"></span><?php echo _MY_COLLEAGUES;?></a></li>
	<li><a href="my_contacts.php"><span class="fa fa-users"></span><?php echo _MY_CONTACTS;?></a></li>
    <li><a href="my_profile.php"><span class="fa fa-user"></span><?php echo _MY_INFO;?></a></li>
    <li><a href="about.php"><span class="fa fa-question-circle"></span><?php echo _MAARCH_CREDITS;?></a></li>
	<li><a href="info.php"><span class="fa fa-exclamation-circle"></span><?php echo _MAARCH_INFO;?></a></li>
    <li><a href="logout.php"><span class="fa fa-power-off"></span><?php echo _LOGOUT;?></a></li>
    <!-- <li><a href="test.php"><span class="fa fa-power-off"></span>INFOS DEVICE</a></li> -->
</ul>
