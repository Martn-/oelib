<?php
/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * This class represents an exception that should be thrown when a database
 * error has occurred.
 *
 * The exception automatically will use an error message, the error message
 * from the DB and the last query.
 *
 * @package TYPO3
 * @subpackage tx_oelib
 *
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
class tx_oelib_Exception_Database extends Exception {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$message = 'There was an error with the database query.' . LF .
			$GLOBALS['TYPO3_DB']->sql_error();

		if ($GLOBALS['TYPO3_DB']->store_lastBuiltQuery
			|| $GLOBALS['TYPO3_DB']->debugOutput
		) {
			$message .= LF . 'The last built query:' . LF .
				$GLOBALS['TYPO3_DB']->debug_lastBuiltQuery;
		}

		parent::__construct($message);
	}
}