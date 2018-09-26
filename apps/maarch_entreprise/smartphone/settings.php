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
?>
<div id="settings" title="Settings" class="panel">
    <h2>Playback</h2>
    <fieldset>
        <div class="row">
            <label>Repeat</label>
            <div class="toggle" onclick="">
                <span class="thumb"></span>
                <span class="toggleOn">ON</span>
                <span class="toggleOff">OFF</span>
            </div>
        </div>
        <div class="row">
            <label>Shuffle</label>
            <div class="toggle" onclick="" toggled="true">
                <span class="thumb"></span>
                <span class="toggleOn">ON</span>
                <span class="toggleOff">OFF</span>
            </div>
        </div>
    </fieldset>
</div>
