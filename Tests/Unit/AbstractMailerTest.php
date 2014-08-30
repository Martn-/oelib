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
 * Test case.
 *
 * @package TYPO3
 * @subpackage tx_oelib
 *
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
class Tx_Oelib_AbstractMailerTest extends Tx_Phpunit_TestCase {
	/**
	 * @var Tx_Oelib_EmailCollector
	 */
	private $subject = NULL;

	/**
	 * @var t3lib_mail_Message
	 */
	private $message1 = NULL;

	/**
	 * @var t3lib_mail_Message
	 */
	private $message2 = NULL;

	/**
	 * @var string[]
	 */
	private $email = array(
		'recipient' => 'any-recipient@email-address.org',
		'subject' => 'any subject',
		'message' => 'any message',
		'headers' => '',
	);

	/**
	 * @var string[]
	 */
	private $otherEmail = array(
		'recipient' => 'any-other-recipient@email-address.org',
		'subject' => 'any other subject',
		'message' => 'any other message',
		'headers' => '',
	);

	protected function setUp() {
		$this->subject = new Tx_Oelib_EmailCollector();

		$this->message1 = $this->getMock('t3lib_mail_Message', array('send', '__destruct'));
		t3lib_div::addInstance('t3lib_mail_Message', $this->message1);
		$this->message2 = $this->getMock('t3lib_mail_Message', array('send', '__destruct'));
		t3lib_div::addInstance('t3lib_mail_Message', $this->message2);
	}

	protected function tearDown() {
		$this->subject->cleanUp();
		t3lib_div::purgeInstances();
		unset($this->subject, $this->message1, $this->message2);
	}


	/*
	 * Utility functions
	 */

	/**
	 * Gets the current character set in TYPO3, e.g., "utf-8".
	 *
	 * @return string the current character set, will not be empty
	 */
	private function getCharacterSet() {
		if (t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) >= 4007000) {
			return 'utf-8';
		}

		return ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] != '') ?
			$GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : 'utf-8';
	}


	/*
	 * Tests concerning sendEmail
	 */

	/**
	 * @test
	 */
	public function storeNoEmailAndTryToGetTheLastEmail() {
		$this->assertSame(
			array(),
			$this->subject->getLastEmail()
		);
	}

	/**
	 * @test
	 */
	public function storeAnEmailAndGetIt() {
		$this->subject->sendEmail(
			$this->email['recipient'],
			$this->email['subject'],
			$this->email['message'],
			$this->email['headers']
		);

		$this->assertSame(
			$this->email,
			$this->subject->getLastEmail()
		);
	}

	/**
	 * @test
	 */
	public function storeTwoEmailsAndGetTheLastEmail() {
		$this->subject->sendEmail(
			$this->email['recipient'],
			$this->email['subject'],
			$this->email['message']
		);
		$this->subject->sendEmail(
			$this->otherEmail['recipient'],
			$this->otherEmail['subject'],
			$this->otherEmail['message']
		);

		$this->assertSame(
			$this->otherEmail,
			$this->subject->getLastEmail()
		);
	}

	/**
	 * @test
	 */
	public function storeTwoEmailsAndGetBothEmails() {
		$this->subject->sendEmail(
			$this->email['recipient'],
			$this->email['subject'],
			$this->email['message'],
			$this->email['headers']
		);
		$this->subject->sendEmail(
			$this->otherEmail['recipient'],
			$this->otherEmail['subject'],
			$this->otherEmail['message']
		);

		$this->assertSame(
			array(
				$this->email,
				$this->otherEmail
			),
			$this->subject->getAllEmail()
		);
	}

	/**
	 * @test
	 */
	public function sendEmailReturnsTrueIfTheReturnValueIsSetToTrue() {
		$this->subject->setFakedReturnValue(TRUE);

		$this->assertTrue(
			$this->subject->sendEmail('', '', '')
		);
	}

	/**
	 * @test
	 */
	public function sendEmailReturnsFalseIfTheReturnValueIsSetToFalse() {
		$this->subject->setFakedReturnValue(FALSE);

		$this->assertFalse(
			$this->subject->sendEmail('', '', '')
		);
	}

	/**
	 * @test
	 */
	public function getLastRecipientReturnsTheRecipientOfTheLastEmail() {
		$this->subject->sendEmail(
			$this->email['recipient'],
			$this->email['subject'],
			$this->email['message']
		);

		$this->assertSame(
			$this->email['recipient'],
			$this->subject->getLastRecipient()
		);
	}

	/**
	 * @test
	 */
	public function getLastRecipientReturnsAnEmptyStringIfThereWasNoEmail() {
		$this->assertSame(
			'',
			$this->subject->getLastRecipient()
		);
	}

	/**
	 * @test
	 */
	public function getLastSubjectReturnsTheSubjectOfTheLastEmail() {
		$this->subject->sendEmail(
			$this->email['recipient'],
			$this->email['subject'],
			$this->email['message']
		);

		$this->assertSame(
			$this->email['subject'],
			$this->subject->getLastSubject()
		);
	}

	/**
	 * @test
	 */
	public function getLastSubjectReturnsAnEmptyStringIfThereWasNoEmail() {
		$this->assertSame(
			'',
			$this->subject->getLastSubject()
		);
	}

	/**
	 * @test
	 */
	public function getLastBodyReturnsTheBodyOfTheLastEmail() {
		$this->subject->sendEmail(
			$this->email['recipient'],
			$this->email['subject'],
			$this->email['message']
		);

		$this->assertSame(
			$this->email['message'],
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function getLastBodyReturnsAnEmptyStringIfThereWasNoEmail() {
		$this->assertSame(
			'',
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function getLastHeadersIfTheEmailDoesNotHaveAny() {
		$this->subject->sendEmail(
			$this->otherEmail['recipient'],
			$this->otherEmail['subject'],
			$this->otherEmail['message']
		);

		$this->assertSame(
			'',
			$this->subject->getLastHeaders()
		);
	}

	/**
	 * @test
	 */
	public function getLastHeadersReturnsTheLastHeaders() {
		$this->subject->sendEmail(
			$this->email['recipient'],
			$this->email['subject'],
			$this->email['message'],
			$this->email['headers']
		);

		$this->assertSame(
			$this->email['headers'],
			$this->subject->getLastHeaders()
		);
	}


	/*
	 * Tests concerning mail
	 */

	/**
	 * @test
	 */
	public function mailWithEmptySenderThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'$emailAddress must not be empty.'
		);

		$this->subject->mail('', 'subject', 'message');
	}

	/**
	 * @test
	 */
	public function mailWithEmptySubjectThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'$subject must not be empty.'
		);

		$this->subject->mail('john@doe.com', '', 'message');
	}

	/**
	 * @test
	 */
	public function mailWithEmptyMessageThrowsException() {
		$this->setExpectedException(
			'InvalidArgumentException',
			'$message must not be empty.'
		);

		$this->subject->mail('john@doe.com', 'subject', '');
	}


	/*
	 * Tests concerning send
	 */

	/**
	 * @test
	 */
	public function getSentEmailsWithoutAnyEmailReturnsEmptyArray() {
		$this->assertSame(
			array(),
			$this->subject->getSentEmails()
		);
	}

	/**
	 * @test
	 */
	public function getNumberOfSentEmailsWithoutAnyEmailReturnsZero() {
		$this->assertSame(
			0,
			$this->subject->getNumberOfSentEmails()
		);
	}

	/**
	 * @test
	 */
	public function getFirstSentEmailWithoutAnyEmailReturnsNull() {
		$this->assertNull(
			$this->subject->getFirstSentEmail()
		);
	}

	/**
	 * @test
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage $email must have a sender set.
	 */
	public function sendWithoutSenderThrowsException() {
		$email = new Tx_Oelib_Mail();
		$email->setSubject('Everybody is happy!');
		$email->setMessage('That is the way it is.');

		$emailRole = $recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', 'john@example.com');
		$email->addRecipient($emailRole);

		$this->subject->send($email);
	}

	/**
	 * @test
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage The e-mail must have at least one recipient.
	 */
	public function sendWithoutRecipientThrowsException() {
		$email = new Tx_Oelib_Mail();
		$email->setSubject('Everybody is happy!');
		$email->setMessage('That is the way it is.');

		$emailRole = $recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', 'john@example.com');
		$email->setSender($emailRole);

		$this->subject->send($email);
	}

	/**
	 * @test
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage The e-mail subject must not be empty.
	 */
	public function sendWithoutSubjectThrowsException() {
		$email = new Tx_Oelib_Mail();
		$email->setMessage('That is the way it is.');

		$emailRole = $recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', 'john@example.com');
		$email->setSender($emailRole);
		$email->addRecipient($emailRole);

		$this->subject->send($email);
	}

	/**
	 * @test
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage The e-mail message must not be empty.
	 */
	public function sendWithoutMessageThrowsException() {
		$email = new Tx_Oelib_Mail();
		$email->setSubject('Everybody is happy!');

		$emailRole = $recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', 'john@example.com');
		$email->setSender($emailRole);
		$email->addRecipient($emailRole);

		$this->subject->send($email);
	}

	/**
	 * @test
	 */
	public function sendSetsSenderNameAndEmail() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$this->subject->send($eMail);

		$sentEmail = $this->subject->getFirstSentEmail();
		$this->assertSame(
			array($sender->getEmailAddress() => $sender->getName()),
			$sentEmail->getFrom()
		);
	}

	/**
	 * @test
	 */
	public function sendSetsRecipientNameAndEmail() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$this->subject->send($eMail);

		$sentEmail = $this->subject->getFirstSentEmail();
		$this->assertSame(
			array($recipient->getEmailAddress() => $recipient->getName()),
			$sentEmail->getTo()
		);
	}

	/**
	 * @test
	 */
	public function sendForTwoRecipientsSendsTwoEmails() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);
		$recipient1 = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', 'joe@example.com');
		$eMail->addRecipient($recipient1);
		$recipient2 = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('Jane Doe', 'jane@example.com');
		$eMail->addRecipient($recipient2);

		$this->subject->send($eMail);

		$this->assertSame(
			2,
			$this->subject->getNumberOfSentEmails()
		);
	}

	/**
	 * @test
	 */
	public function sendSetsSubject() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$this->subject->send($eMail);

		$sentEmail = $this->subject->getFirstSentEmail();
		$this->assertSame(
			$this->email['subject'],
			$sentEmail->getSubject()
		);
	}

	/**
	 * @test
	 */
	public function sendingPlainTextMailUsesDefaultCharacterSet() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$this->subject->send($eMail);

		$this->assertSame(
			$this->getCharacterSet(),
			$this->subject->getFirstSentEmail()->getCharset()
		);
	}

	/**
	 * @test
	 */
	public function sendSetsPlainTextBody() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$this->subject->send($eMail);

		$sentEmail = $this->subject->getFirstSentEmail();
		$this->assertSame(
			$this->email['message'],
			$sentEmail->getBody()
		);
	}

	/**
	 * @test
	 */
	public function sendingPlainTextMailUsesPlainTextEncoding() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$this->subject->send($eMail);

		$this->assertSame(
			'text/plain',
			$this->subject->getFirstSentEmail()->getContentType()
		);
	}

	/**
	 * @test
	 */
	public function sendingPlainTextMailByDefaultRemovesAnyCarriageReturnFromBody() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage(
			'one long line ...........................................' . CRLF .
			'now a blank line:' . LF . LF .
			'another long line .........................................' . LF .
			'and a line with umlauts: Hörbär saß früh.'
		);

		$this->subject->send($eMail);

		$this->assertNotContains(
			CR,
			$this->subject->getFirstSentEmail()->getBody()
		);
	}

	/**
	 * @test
	 */
	public function sendingPlainTextMailWithFormattingRemovesAnyCarriageReturnFromBody() {
		$this->subject->sendFormattedEmails(TRUE);

		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage(
			'one long line ...........................................' . CRLF .
			'now a blank line:' . LF . LF .
			'another long line .........................................' . LF .
			'and a line with umlauts: Hörbär saß früh.'
		);

		$this->subject->send($eMail);

		$this->assertNotContains(
			CR,
			$this->subject->getFirstSentEmail()->getBody()
		);
	}

	/**
	 * @test
	 */
	public function sendingPlainTextMailWithoutFormattingNotRemovesAnyCarriageReturnFromBody() {
		$this->subject->sendFormattedEmails(FALSE);

		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage(
			'one long line ...........................................' . CRLF .
			'now a blank line:' . LF . LF .
			'another long line .........................................' . LF .
			'and a line with umlauts: Hörbär saß früh.'
		);

		$this->subject->send($eMail);

		$this->assertContains(
			CR,
			$this->subject->getFirstSentEmail()->getBody()
		);
	}

	/**
	 * @test
	 */
	public function sendSetsHtmlBody() {
		$htmlMessage = '<h1>Very cool HTML message</h1>' . LF . '<p>Great to have HTML e-mails in oelib.</p>';
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage('This is the plain text message.');
		$eMail->setHTMLMessage($htmlMessage);

		$this->subject->send($eMail);

		$children = $this->subject->getFirstSentEmail()->getChildren();
		/** @var Swift_Mime_MimeEntity $firstChild */
		$firstChild = $children[0];
		$this->assertSame(
			$htmlMessage,
			$firstChild->getBody()
		);
	}

	/**
	 * @test
	 */
	public function sendSetsHtmlBodyWithTextHtmlContentType() {
		$htmlMessage = '<h1>Very cool HTML message</h1>' . LF . '<p>Great to have HTML e-mails in oelib.</p>';
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage('This is the plain text message.');
		$eMail->setHTMLMessage($htmlMessage);

		$this->subject->send($eMail);

		$children = $this->subject->getFirstSentEmail()->getChildren();
		/** @var Swift_Mime_MimeEntity $firstChild */
		$firstChild = $children[0];
		$this->assertSame(
			'text/html',
			$firstChild->getContentType()
		);
	}

	/**
	 * @test
	 */
	public function sendWithReturnPathSetsReturnPath() {
		$returnPath = 'return@example.com';

		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);
		$eMail->setReturnPath($returnPath);

		$this->subject->send($eMail);

		$sentEmail = $this->subject->getFirstSentEmail();
		$this->assertSame(
			$returnPath,
			$sentEmail->getReturnPath()
		);
	}

	/**
	 * @test
	 */
	public function sendWithoutReturnPathNotSetsReturnPath() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$this->subject->send($eMail);

		$sentEmail = $this->subject->getFirstSentEmail();
		$this->assertNull(
			$sentEmail->getReturnPath()
		);
	}

	/**
	 * @test
	 */
	public function sendCanAddOneAttachmentFromFile() {
		$attachment = new Tx_Oelib_Attachment();
		$attachment->setFileName(t3lib_extMgm::extPath('oelib', 'Tests/Unit/Fixtures/test.txt'));
		$attachment->setContentType('text/plain');

		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$eMail->addAttachment($attachment);

		$this->subject->send($eMail);
		$children = $this->subject->getFirstSentEmail()->getChildren();
		/** @var Swift_Mime_Attachment $firstChild */
		$firstChild = $children[0];

		$this->assertSame(
			'some text',
			$firstChild->getBody()
		);
		$this->assertSame(
			'text/plain',
			$firstChild->getContentType()
		);
	}

	/**
	 * @test
	 */
	public function sendCanAddOneAttachmentFromContent() {
		$content = '<p>Hello world!</p>';
		$attachment = new Tx_Oelib_Attachment();
		$attachment->setContent($content);
		$attachment->setContentType('text/html');

		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$eMail->addAttachment($attachment);

		$this->subject->send($eMail);
		$children = $this->subject->getFirstSentEmail()->getChildren();
		/** @var Swift_Mime_Attachment $firstChild */
		$firstChild = $children[0];

		$this->assertSame(
			$content,
			$firstChild->getBody()
		);
		$this->assertSame(
			'text/html',
			$firstChild->getContentType()
		);
	}

	/**
	 * @test
	 */
	public function sendCanAddOneAttachmentWithFilenameFromContent() {
		$content = '<p>Hello world!</p>';
		$fileName = 'greetings.html';
		$attachment = new Tx_Oelib_Attachment();
		$attachment->setContent($content);
		$attachment->setFileName($fileName);
		$attachment->setContentType('text/html');

		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$eMail->addAttachment($attachment);

		$this->subject->send($eMail);
		$children = $this->subject->getFirstSentEmail()->getChildren();
		/** @var Swift_Mime_Attachment $firstChild */
		$firstChild = $children[0];

		$this->assertSame(
			$content,
			$firstChild->getBody()
		);
		$this->assertSame(
			$fileName,
			$firstChild->getFilename()
		);
		$this->assertSame(
			'text/html',
			$firstChild->getContentType()
		);
	}

	/**
	 * @test
	 */
	public function sendCanAddTwoAttachments() {
		$sender = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('', 'any-sender@email-address.org');
		$recipient = new Tx_Oelib_Tests_Unit_Fixtures_TestingMailRole('John Doe', $this->email['recipient']);
		$eMail = new Tx_Oelib_Mail();
		$eMail->setSender($sender);
		$eMail->addRecipient($recipient);
		$eMail->setSubject($this->email['subject']);
		$eMail->setMessage($this->email['message']);

		$attachment1 = new Tx_Oelib_Attachment();
		$attachment1->setFileName(t3lib_extMgm::extPath('oelib', 'Tests/Unit/Fixtures/test.txt'));
		$attachment1->setContentType('text/plain');
		$eMail->addAttachment($attachment1);
		$attachment2 = new Tx_Oelib_Attachment();
		$attachment2->setFileName(t3lib_extMgm::extPath('oelib', 'Tests/Unit/Fixtures/test_2.css'));
		$attachment2->setContentType('text/css');
		$eMail->addAttachment($attachment2);

		$this->subject->send($eMail);
		$children = $this->subject->getFirstSentEmail()->getChildren();

		$this->assertSame(
			2,
			count($children)
		);
	}


	/*
	 * Tests concerning formatting the e-mail body.
	 */

	/**
	 * @test
	 */
	public function oneLineFeedIsKeptIfFormattingIsEnabled() {
		$this->subject->sendEmail('', '', 'foo' . LF . 'bar');

		$this->assertSame(
			'foo' . LF . 'bar',
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function oneCarriageReturnIsReplacedByLfIfFormattingIsEnabled() {
		$this->subject->sendEmail('', '', 'foo' . CR . 'bar');

		$this->assertSame(
			'foo' . LF . 'bar',
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function twoLineFeedsAreKeptIfFormattingIsEnabled() {
		$this->subject->sendEmail('', '', 'foo' . LF . LF . 'bar');

		$this->assertSame(
			'foo' . LF . LF . 'bar',
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function twoCarriageReturnsAreReplacedByTwoLfIfFormattingIsEnabled() {
		$this->subject->sendEmail('', '', 'foo' . CR . CR . 'bar');

		$this->assertSame(
			'foo' . LF . LF . 'bar',
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function severalLineFeedsAreReplacedByTwoLfIfFormattingIsEnabled() {
		$this->subject->sendEmail('', '', 'foo' . LF . LF . LF . LF . LF . 'bar');

		$this->assertSame(
			'foo' . LF . LF . 'bar',
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function severalCarriageReturnsAreReplacedByTwoLfIfFormattingIsEnabled() {
		$this->subject->sendEmail('', '', 'foo' . CR . CR . CR . CR . CR . 'bar');

		$this->assertSame(
			'foo' . LF . LF . 'bar',
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function emailBodyIsNotChangesWhenFormattingIsDisabled() {
		$this->subject->sendFormattedEmails(FALSE);
		$this->subject->sendEmail('', '', 'foo' . CR . CR . CR . CR . CR . 'bar');

		$this->assertSame(
			'foo' . CR . CR . CR . CR . CR . 'bar',
			$this->subject->getLastBody()
		);
	}

	/**
	 * @test
	 */
	public function oneCrLfPairIsReplacedByLfIfFormattingIsEnabled() {
		$this->subject->sendEmail('', '', 'foo' . CRLF . 'bar');

		$this->assertSame(
			'foo' . LF . 'bar',
			$this->subject->getLastBody()
		);
	}
}