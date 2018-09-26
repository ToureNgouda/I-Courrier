<?php
/**
* Copyright Maarch since 2008 under licence GPLv3.
* See LICENCE.txt file at the root folder for more details.
* This file is part of Maarch software.
*
*/

/**
* @brief class of install tools
*
* @file
* @author Arnaud Veber
* @date $date$
* @version $Revision$
* @ingroup install
*/
if ($_SESSION['user']['UserId'] <> 'superadmin') {
    header('location: ' . $_SESSION['config']['businessappurl']
        . 'index.php?page=update_control&admin=update_control');
    exit();
}
?>
<div class="blockWrapper">
    <div class="titleBlock">
        <h2 onClick="slide('chooseLanguage');" style="cursor: pointer;">
            <?php echo _CHOOSE_LANGUAGE;?>
        </h2>
    </div>
    <div class="contentBlock" id="chooseLanguage">
        <p>
            <form action="scripts/language_update.php" method="post">
                <select name="languageSelect" id="languageSelect" onChange="checkLanguage(this.value)">
                    <option value="default">Select a language</option>
                    <?php
                        for($i=0; $i<count($listLang);$i++) {
                            echo '<option ';
                              echo 'value="'.$listLang[$i].'"';
                            echo '>';
                                if ($listLang[$i] == "fr") {
                                    echo "French";
                                } else if ($listLang[$i] == "en") {
                                    echo "English";
                                } else {
                                    echo $listLang[$i];
                                }
                            echo '</option>';
                        }
                    ?>
                </select>
            </form>
        </p>
    </div>
</div>
<br />
<div class="blockWrapper">
    <div class="contentBlock" id="chooseLanguage">
        <p>
            <div id="buttons">
                <div style="float: left;" class="previousButton">
                </div>
                <div style="float: right;" class="nextButton" id="next">
                    <span id="returnCheckLanguage" style="display: none;">
                        <a href="#" onClick="$('form').submit();">
                            <?php echo _NEXT_INSTALL;?>
                        </a>
                    </span>
                </div>
            </div>
            <br />
            <br />
        </p>
    </div>
</div>
