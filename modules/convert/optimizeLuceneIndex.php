<?php

/*
*   Copyright 2016 Maarch
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
*/

/**
* @brief process fulltext class
*
* <ul>
* <li>Services to process the fulltext of resources</li>
* </ul>
*
* @file
* @author <dev@maarch.org>
* @date $date$
* @version $Revision$
* @ingroup convert
*/

	if(strpos($_SERVER['argv'][1], '/indexes/') >= 0){

		$_ENV['maarch_tools_path'] = $_SERVER['argv'][2].'/apps/maarch_entreprise/tools/';

		// Storing text in lucene index
		set_include_path('../../../apps/maarch_entreprise/tools/' . PATH_SEPARATOR . get_include_path());

		if(!@include('Zend/Search/Lucene.php')) {
		    set_include_path('apps/maarch_entreprise/tools/'. PATH_SEPARATOR . get_include_path()
		    );
		    require_once("Zend/Search/Lucene.php");
		}
		require_once 'Zend/Search/Lucene/Storage/File/Filesystem.php';
		require_once 'Zend/Search/Lucene/Storage/Directory/Filesystem.php';

		$directory = new Zend_Search_Lucene_Storage_Directory_Filesystem((string) $_SERVER['argv'][1]);

		$testDir = Zend_Search_Lucene::getActualGeneration($directory);

		if ($testDir != -1) {
			$index = Zend_Search_Lucene::open((string) $_SERVER['argv'][1]);

			if (!empty($index)) {
				$index->setFormatVersion(Zend_Search_Lucene::FORMAT_2_3);
				Zend_Search_Lucene_Analysis_Analyzer::setDefault(
					new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive()
				);
				$index->optimize();
			}
		}

	}


?>
