<?php
/***************************************************************
* Copyright notice
*
* (c) 2009 Niels Pardon (mail@niels-pardon.de)
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class 'tx_oelib_Mail' for the 'oelib' extension.
 *
 * This class represents an e-mail.
 *
 * @package TYPO3
 * @subpackage tx_oelib
 *
 * @author Niels Pardon <mail@niels-pardon.de>
 */
class tx_oelib_Mail extends tx_oelib_Object {
	/**
	 * @var tx_oelib_Interface_MailRole the sender of the e-mail
	 */
	private $sender = null;

	/**
	 * @var array the recipients of the e-mail
	 */
	private $recipients = array();

	/**
	 * @var array the data of this object
	 */
	private $data = array();

	/**
	 * @var array attachments of the e-mail
	 */
	private $attachments = array();

	/**
	 * Frees as much memory that has been used by this object as possible.
	 */
	public function __destruct() {
		foreach ($this->attachments as $key => $attachment) {
			$attachment->__destruct();
			unset($this->attachments[$key]);
		}

		unset($this->data, $this->sender, $this->recipients);
	}

	/**
	 * Sets the value of the data item for the key $key.
	 *
	 * @param string the key of the data item to set, must not be empty
	 * @param mixed the data for the key $key
	 */
	protected function set($key, $value) {
		$this->data[$key] = $value;
	}

	/**
	 * Gets the value of the data item for the key $key.
	 *
	 * @param string the key of the data item to get, must not be empty
	 *
	 * @return mixed the data for the key $key, will be an empty string
	 *               if the key has not been set yet
	 */
	protected function get($key) {
		if (!isset($this->data[$key])) {
			return '';
		}

		return $this->data[$key];
	}

	/**
	 * Sets the sender of the e-mail.
	 *
	 * @param tx_oelib_Interface_MailRole the sender of the e-mail
	 */
	public function setSender(tx_oelib_Interface_MailRole $sender) {
		$this->sender = $sender;
	}

	/**
	 * Returns the sender of the e-mail.
	 *
	 * @return tx_oelib_Interface_MailRole the sender of the e-mail, will be
	 *                                     null if the sender has not been set
	 */
	public function getSender() {
		return $this->sender;
	}

	/**
	 * Returns whether the e-mail has a sender.
	 *
	 * @return boolean true if the e-mail has a sender, false otherwise
	 */
	public function hasSender() {
		return is_object($this->sender);
	}

	/**
	 * Adds a recipient for the e-mail.
	 *
	 * @param tx_oelib_Interface_MailRole a recipient for the e-mail, must not be empty
	 */
	public function addRecipient(tx_oelib_Interface_MailRole $recipient) {
		$this->recipients[] = $recipient;
	}

	/**
	 * Returns the recipients of the e-mail.
	 *
	 * @return array the recipients of the e-mail, will be empty if no
	 *               recipients have been set
	 */
	public function getRecipients() {
		return $this->recipients;
	}

	/**
	 * Sets the subject of the e-mail.
	 *
	 * @param string the subject of the e-mail, must not be empty
	 */
	public function setSubject($subject) {
		if ($subject == '') {
			throw new Exception('$subject must not be empty.');
		}

		if (strpos($subject, CR) !== false || strpos($subject, LF) !== false) {
			throw new Exception(
				'$subject must not contain any line breaks or carriage returns.'
			);
		}

		$this->setAsString('subject', $subject);
	}

	/**
	 * Returns the subject of the e-mail.
	 *
	 * @return string the subject of the e-mail, will be empty if the subject has
	 *                not been set
	 */
	public function getSubject() {
		return $this->getAsString('subject');
	}

	/**
	 * Sets the message of the e-mail.
	 *
	 * @param string the message of the e-mail, must not be empty
	 */
	public function setMessage($message) {
		if ($message == '') {
			throw new Exception('$message must not be empty.');
		}

		$this->setAsString('message', $message);
	}

	/**
	 * Returns the message of the e-mail.
	 *
	 * @return string the message of the e-mail, will be empty if the message has
	 *                not been set
	 */
	public function getMessage() {
		return $this->getAsString('message');
	}

	/**
	 * Returns whether the e-mail has a message.
	 *
	 * @return boolean true if the e-mail has a message, false otherwise
	 */
	public function hasMessage() {
		return $this->hasString('message');
	}

	/**
	 * Sets the HTML message of the e-mail.
	 *
	 * @param string the HTML message of the e-mail, must not be empty
	 */
	public function setHTMLMessage($message) {
		if ($message == '') {
			throw new Exception('$message must not be empty.');
		}

		$this->setAsString('html_message', $message);
	}

	/**
	 * Returns the HTML message of the e-mail.
	 *
	 * @return string the HTML message of the e-mail, will be empty if the
	 *                message has not been set
	 */
	public function getHTMLMessage() {
		return $this->getAsString('html_message');
	}

	/**
	 * Returns whether the e-mail has an HTML message.
	 *
	 * @return string true if the e-mail has an HTML message, false otherwise
	 */
	public function hasHTMLMessage() {
		return $this->hasString('html_message');
	}

	/**
	 * Adds an attachment to the e-mail.
	 *
	 * @param tx_oelib_Attachment the attachment to add
	 */
	public function addAttachment(tx_oelib_Attachment $attachment) {
		$this->attachments[] = $attachment;
	}

	/**
	 * Returns the attachments of the e-mail.
	 *
	 * @return array the attachments of the e-mail, might be empty
	 */
	public function getAttachments() {
		return $this->attachments;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/oelib/class.tx_oelib_Mail.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/oelib/class.tx_oelib_Mail.php']);
}
?>