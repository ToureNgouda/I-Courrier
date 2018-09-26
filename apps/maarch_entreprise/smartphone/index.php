<?php
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (
    !isset($_SESSION['config']['corepath'])
    || !isset($_SESSION['config']['databasename'])
) {
    header('location: ../../../');
}
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
$core = new core_tools();
$core->load_lang();
//var_dump($_REQUEST);
if ($_REQUEST['display'] <> 'true') {
?>
	<!DOCTYPE html>
	<html manifest="iphone.manifest">
		<?php
		    $core->loadSmartphoneHeader();
		?>
		<script type="text/javascript">
			var myScroll;
			var a = 0;
			function loaded()
			{
				myScroll = new iScroll('scroller', {desktopCompatibility:true});
			}
			//Load iScroll when DOM content is ready.
			//document.addEventListener('DOMContentLoaded', loaded, false);

			/*function toggle_reinitialisation() {

				alert('OK');
				var nbLineReset.value= parseFloat(5);
			}*/



		</script>
		<body>
			<div class="toolbar"> 
				<h1 id="pageTitle"></h1>
				<?php
				if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
					?>
					<a id="backButton" class="button" onclick="clean();"  href="#"></a>
					<?php
				}
				?>
				<!-- <a id="maarchLogo" class="maarchLogo" href="index.php?page=welcome" target="_webapp">&nbsp;&nbsp;&nbsp;&nbsp;</a> -->
				<!--<a class="button" href="#searchForm">Search</a>-->
			</div>
			<?php
			    include($_REQUEST['page'] . '.php');
			?>
			<div class="error">
				<?php
				if (isset($_SESSION['error'])) {
					echo $_SESSION['error'];
				}
				$_SESSION['error'] = '';
				?>
			</div>
			<!--<div class="footer" id="footer" selected="true">
				<h1 id="pageTitle">hello</h1>
			</div>-->
		</body>
	</html>
<?php
}
?>


