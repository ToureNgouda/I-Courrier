<?php
include_once '../../../core/init.php';
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
?>
<!DOCTYPE html>
<html manifest="iphone.manifest">
    <?php
        $core->loadSmartphoneHeader();
    ?>
    <body>
        <div class="toolbar">
            <h1 id=""><?php echo _LOGIN; ?></h1>
            <!-- <a id="maarchLogo" class="maarchLogo" href="#">&nbsp;&nbsp;&nbsp;&nbsp;</a> -->
        </div>
        <form selected="true" title="Login" class="panel" method="post" action="<?php
            echo $_SESSION['config']['businessappurl']
            . 'index.php?display=true&amp;page=log&dir=smartphone'
            ;?>" target="_self">
            <fieldset>
					<table>
						<?php
						if ($_REQUEST['withRA_CODE'] != 'true'){
						?>
						<tr>
							<td style="width:50%;text-align:left;">
								<label><b><?php echo _ID; ?></b></label>
							</td>
							<td style="width:50%;">
								<input style="width:100%" type="text" name="login" value="" style="width:100%;"/>
							</td>
						</tr>
					<tr>
                    <td colspan="2"></td>
                    </tr>
						<tr>
							<td style="width:50%;text-align:left;">
								<label><b><?php echo _PASSWORD; ?></b></label>
							</td>
							<td style="width:50%;">
								<input style="width:100%" type="password" name="pass" value="" style="width:100%;" />
							</td>
						</tr>
						<?php
						}
						if ($_REQUEST['withRA_CODE'] == 'true'){
						?>
						<tr style="display:none">
							<td style="width:50%;text-align:left;">
								<label><b><?php echo _ID; ?></b></label>
							</td>
							<td style="width:50%;">
								<input style="text-align:left;" type="text" name="login" value="<?php echo $_SESSION['recup_user']['login']; ?>" style="width:100%;"/>
							</td>
						</tr>
						
						<tr style="display:none">
							<td style="width:50%;text-align:left;">
								<label><b><?php echo _PASSWORD; ?></b></label>
							</td>
							<td style="width:50%;">
								<input type="password" name="pass" value="<?php echo $_SESSION['recup_user']['password']; ?>" style="width:100%;" />
							</td>
						</tr>
						
						<tr>
							<td style="width:50%;text-align:left;">
								<label><b><?php echo _RA_CODE_1; ?></b></label>
							</td>
							<td style="width:50%;">
								<input type="password" name="ra_code" value="" style="width:100%;" />
							</td>
						</tr>
						<?php
						}
						unset($_SESSION['recup_user']);
						?>
					</table>
           
            </fieldset>
            <input class="whiteButton" type="submit" value="<?php echo _CONNECT; ?>">
            <div class="error">
                <?php
                if (isset($_SESSION['error'])) {
                    echo $_SESSION['error'];
                }
                $_SESSION['error'] = '';
                ?>
            </div>
        </form>
        <div class="toolbar" style="border-top: 1px solid #2d3642;color:#0487C1;display: block;position: absolute;border: 0px;height: auto;bottom: 0px;width: 100%;padding: 10px;font-size: 8px;text-align: center;">
           
            <?php echo _MEP_VERSION; ?>
        </div>
    </body>
</html>

