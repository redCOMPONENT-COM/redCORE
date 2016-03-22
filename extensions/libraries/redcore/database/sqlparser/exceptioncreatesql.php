<?php
/**
 * @package     Redcore
 * @subpackage  Exception
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * This file implements some expection classes which are used within the
 * RDatabaseSqlparserSqlparser package.
 *
 * Copyright (c) 2010-2012, Justin Swanhart
 * with contributions by André Rothe <arothe@phosco.info, phosco@gmx.de>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 */

/**
 * SQL Parser Exception
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.0
 */
class RDatabaseSqlparserExceptioncreatesql extends Exception
{
	protected $part;

	protected $partkey;

	protected $entry;

	protected $entrykey;

	/**
	 * Constructor
	 *
	 * @param   string  $part      Part
	 * @param   string  $partkey   partkey
	 * @param   string  $entry     entry
	 * @param   string  $entrykey  entrykey
	 *
	 * @since   1.2
	 */
	public function __construct($part, $partkey, $entry, $entrykey)
	{
		$this->part = $part;
		$this->partkey = $partkey;
		$this->entry = $entry;
		$this->entrykey = $entrykey;
		parent::__construct("unknown " . $entrykey . " in " . $part . "[" . $partkey . "] " . (empty($entry[$entrykey]) ? '' : $entry[$entrykey]), 15);
	}

	/**
	 * Gets entry
	 *
	 * @return  string
	 */
	public function getEntry()
	{
		return $this->entry;
	}

	/**
	 * Gets entry key
	 *
	 * @return  string
	 */
	public function getEntryKey()
	{
		return $this->entrykey;
	}

	/**
	 * Gets SQL Part
	 *
	 * @return  string
	 */
	public function getSQLPart()
	{
		return $this->part;
	}

	/**
	 * Gets SQL Part key
	 *
	 * @return  string
	 */
	public function getSQLPartKey()
	{
		return $this->partkey;
	}
}
