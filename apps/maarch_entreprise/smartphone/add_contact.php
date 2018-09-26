<?php
if (file_exists('../../../core/init.php')) {
    include_once '../../../core/init.php';
}
if (!isset($_SESSION['config']['corepath'])) {
    header('location: ../../../');
}
require_once('core/class/class_functions.php');
require_once('core/class/class_core_tools.php');
require_once('core/class/class_db_pdo.php');
require_once('core/core_tables.php');
require_once('apps/maarch_entreprise/apps_tables.php');
require_once('apps/maarch_entreprise/lang/fr.php');

$core = new core_tools();
$core->load_lang();

?>
<div id="addContact" title="<?php echo _ADD_CONTACT ?>" class="panel">
    <h2><span class="fa fa-user"></span></a>Infos</h2>
    <form selected="true" class="panel" method="post" action="query_add_contact.php">
        <fieldset>
            <input type="hidden" name="entity_id"
                   value="<?php functions::xecho($_SESSION['user']['entities'][0]["ENTITY_ID"]); ?>">
            <div class="row">
                <label><?php echo _IS_CORPORATE_PERSON; ?></label>
                <div class="toggle" onclick="cacher('person', 'lastname', 'firstname')"><span class="thumb"></span><span
                            class="toggleOn">ON</span><span class="toggleOff">OFF</span></div>
            </div>
            <div id="person" class="row" style="display: block;">
                <div class="row">
                    <table>
                        <tr>
                            <td width="50%" align="left">
                                <label><b><?php echo _CONTACT_TYPE ?></b></label>
                            </td>
                            <td width="50%" align="right">
                                <select name="contact_type" id="contact_type">
                                    <option value="">Choisissez le type de contact</option>
                                    <option value="106" selected="selected">0. Particuliers</option>
                                    <option value="101">2. Associations</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table>
                        <tr>
                            <td width="50%" align="left">
                                <label><b><?php echo _TITLE2 ?></b></label>
                            </td>
                            <td width="50%" align="right">
                                <select name="title" id="title">
                                    <option value="">Choisissez une civilité</option>
                                    <option value="title4">Avocat</option>
                                    <option value="title7">Ecole</option>
                                    <option value="title2">Madame</option>
                                    <option value="title8">Madame et Monsieur</option>
                                    <option value="title3">Mademoiselle</option>
                                    <option value="title6">Mairie</option>
                                    <option value="title9">Messieurs</option>
                                    <option value="title1" selected="selected">Monsieur</option>
                                    <option value="title5">Notaire</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table>
                        <tr>
                            <td width="50%" align="left">
                                <label><b><?php echo _LASTNAME ?></b></label>
                            </td>
                            <td width="50%" align="right">
                                <input name="lastname" id="lastname" required/>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <table>
                        <tr>
                            <td width="50%" align="left">
                                <label><b><?php echo _FIRSTNAME ?></b></label>
                            </td>
                            <td width="50%" align="right">
                                <input name="firstname" id="firstname"/>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _SOCIETY ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input name="society"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _FUNCTION ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input type="text" name="function"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _EMAIL ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input type="text" name="email"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _PHONE_NUMBER ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input type="tel" name="phone"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="center" colspan='2'>
                            <label><b><?php echo _ADDRESS; ?></b></label>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _NUMBER; ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input name="number"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _STREET; ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input name="street" rows="2"></input>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _COMPLEMENT; ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input name="complement" rows="2"></input>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _TOWN; ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input name="town" rows="2"></input>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _POSTAL_CODE; ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input type="text" name="postal_code"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _COUNTRY; ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <input name="country" value="France"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <table>
                    <tr>
                        <td width="50%" align="left">
                            <label><b><?php echo _OTHER ?></b></label>
                        </td>
                        <td width="50%" align="right">
                            <textarea name="other" rows="4"></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <input type="submit" value="<?php echo _ADD ?>" class="whiteButton">
    </form>

</div>