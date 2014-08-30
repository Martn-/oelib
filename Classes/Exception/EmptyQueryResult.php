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
 * query has an empty result, but shouldn't have.
 *
 * The exception automatically will use an error message and the last query.
 *
 * @package TYPO3
 * @subpackage tx_oelib
 *
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
class tx_oelib_Exception_EmptyQueryResult extends Exception {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$message = 'The database query returned an empty result, but should ' .
			'have returned a non-empty result.';

		if ($GLOBALS['TYPO3_DB']->store_lastBuiltQuery
			|| $GLOBALS['TYPO3_DB']->debugOutput
		) {
			$message .= LF . 'The last built query:' . LF .
				$GLOBALS['TYPO3_DB']->debug_lastBuiltQuery;
		}

		parent::__construct($message);
	}
}