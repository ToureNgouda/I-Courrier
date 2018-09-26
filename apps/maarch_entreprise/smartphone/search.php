<?php
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (!isset($_SESSION['config']['corepath'])) {
    header('location: ../../../');
}
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
$core = new core_tools();
$core->load_lang();
$_SESSION['collection_id_choice'] = '';
?>
<div id="search" title="<?php echo _SEARCH;?>" class="panel">
    <form id="search" title="SearchRes" class="panel" action="search_result.php" method="POST" selected="true">
        <fieldset>
            <div class="row">
                <table width="90%">
                    <tr>
                        <td align="center" width="50%">
                            <b><?php echo _COLLECTION;?></b>
                            <select name="collection">
                                    <option value="<?php functions::xecho($_SESSION['collections'][0]['id']);?>">
                                        <?php functions::xecho($_SESSION['collections'][0]['label']);?>
                                    </option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <b><?php echo _FULLTEXT;?></b>
                        </td>
                        <td width="50%">
                            <input style="width: 100%;" type="text" name="fulltext" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" align="left">
                            <b><?php echo _CONTACT." <div style='font-size:9px'>(destinataire / expéditeur)<div>";?></b>
                        </td>
                        <td width="50%">
                            <input style="width: 100%;" type="text" name="contact" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" align="left">
                            <b><?php echo _OBJECT;?></b>
                        </td>
                        <td width="50%">
                            <input style="width: 100%;" type="text" name="subject" value=""/>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <a type="submit" href="#" style="text-decoration:none"><input type="button" class="whiteButton" value="<?php echo _SEARCH;?>"/></a>
    </form>
    <div class="error">
        <?php
        if (isset($_SESSION['error'])) {
            echo $_SESSION['error'];
        }
        $_SESSION['error'] = '';
        ?>
    </div>
</div>
