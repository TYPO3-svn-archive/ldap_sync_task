<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cité Solution (technique@in-cite.net)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Core functions for syncing KEOLIS XML files.
 *
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 */

/**
 * Core functions for syncing KEOLIS XML files.
 *
 * @author	Pierrick Caillon <pierrick@in-cite.net>
 * @package TYPO3
 * @subpackage user_xmlsync
 */
class tx_ldapsynctask_clitask extends t3lib_cli {
	protected $dryrun = false;	/**< Indicates if dryrun mode is activated. */
	const LOCK_FILE = 'typo3temp/tx_ldapsynctask_clitask.lock';	/**< Path for the one instance lock file. */
	private $server;

	/**
	 * \brief Constructor.
	 * Initializes parser modules, settings and cli options.
	 */
	public function __construct()
	{
		parent::t3lib_cli();
		$this->parserModules = (array)$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['user_xmlsync']['parserModules'];

		$this->cli_options[] = array('-h', 'Shows this help.');
		$this->cli_options[] = array('--dryrun', 'Shows which updates would be done.');

			// Setting help texts:
		$this->cli_help['name'] = 'ldap_sync_task -- Test sync task for LDAP servers.';
		$this->cli_help['synopsis'] = '';
		$this->cli_help['description'] = "Not to be used in cron jobs. Use the scheduler task instead.";
		$this->cli_help['examples'] = "/.../cli_dispatch.phpsh xmlsync 1";
		$this->cli_help['author'] = "In Cité Solution, (c) 2011";
	}

	/**************************
	 *
	 * CLI functionality
	 *
	 *************************/

	/**
	 * CLI engine
	 *
	 * @param	array		Command line arguments
	 * @return	string
	 */
	public function cli_main($argv)
	{
		$this->cli_validateArgs();
		$this->parseSettings();
		if (!$this->checkOneInstance())
			return;
		$this->start();
		
		// Enable dryrun mode if asked.
		if (isset($this->cli_args['--dryrun']))
			$this->dryrun = true;
		
		$task = t3lib_div::makeInstance('tx_ldapsynctask_task');
		$task->server = $this->server;
		echo $task->execSync($this->isDryrun());

		$this->clean();
	}
	
	/**
	 * Retrieves the state of the dryrun mode.
	 * @return boolean State of the dryrun mode.
	 */
	public function isDryrun()
	{
		return $this->dryrun;
	}
	
	/**
	 * \brief Tests if the script is already running.
	 * @return <code>true</code> in case of no running instance, otherwise <code>false</code>.
	 */
	protected function checkOneInstance()
	{
		if (file_exists(self::LOCK_FILE) && (filemtime(self::LOCK_FILE) > time() - 3600))
		{
			$this->cli_echo('Already running. If you know it is not, please check the lock file.' . chr(10));
			$this->cli_echo(PATH_site . self::LOCK_FILE . chr(10));
			return false;
		}
		return true;
	}
	
	/**
	 * \brief Initializes the lock file.
	 */
	protected function start()
	{
		touch(PATH_site . self::LOCK_FILE);
	}
	
	/**
	 * \brief Cleans the lock file.
	 */
	protected function clean()
	{
		touch(PATH_site . self::LOCK_FILE, time() - 3600);
	}
	
	protected function parseSettings() {
		if (isset($this->cli_args['-h']))
		{
			$this->cli_help();
			exit;
		}
		$noOptionsArgs = $this->cli_args['_DEFAULT'];
		$myOptions = -1;
		for ($i = 0; $i < count($noOptionsArgs); $i++) {
			switch ($myOptions) {
			case -1:
				if (strpos($noOptionsArgs[$i], 'cli_dispatch') != false)
					$myOptions = 0;
				break;
			case 0:
				if ($noOptionsArgs[$i] != '--') {
					$result = intval($noOptionsArgs[$i]);
					if (!$result) {
						return;
					}
					$this->server = $result;
					$myOptions = 1;
				}
				break;
			}
		}
	}
}
