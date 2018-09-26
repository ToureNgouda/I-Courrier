<?php

/*
*
*   Copyright 2008,2017 Maarch
*
*   This file is part of Maarch Framework.
*
*   Maarch Framework is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Maarch Framework is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Maarch Framework.  If not, see <http://www.gnu.org/licenses/>.
*
*   @author <laurent.giovannoni@maarch.org>
*/
?>

<script src="../../modules/content_management/js/onlyOffice.js" type="text/javascript"></script>
<div class="error" id="divError" name="divError"></div>
<button class="button" id="saveEditor">Save</button> 
<button class="button" id="openPDF" disabled onclick="window.open('tmp/cloudooo_results/final.pdf', '_blank');">Open PDF final version</button>
<div width="100%" height="100%" data-gadget-scope="editor" data-gadget-url="https://text-gadget.app.officejs.com/1.0/" data-gadget-sandbox="iframe"></div>
