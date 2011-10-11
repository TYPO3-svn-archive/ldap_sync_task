<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cité Solution <technique@in-cite.net>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

require_once(t3lib_extMgm::extPath('ldap_sync') . 'class.tx_ldapsync.php');

/**
 * Task ldap synchronisation
 *
 * @author	Prud'homme Emilie <emilie@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_ldapsyn_task
 */
class tx_ldapsynctask_task extends tx_scheduler_Task  {
	
	public $server = array();
	private $writeDevLog = true;
	
	/* 
	 * Task scheduler call function 
	 */
	public function execute() {		
		if (!$this->server) {
			if ($this->writeDevLog) { 
				t3lib_div::devLog('LDAP Sync', 'ldap_sync_task', 2, array($GLOBALS['LANG']->sL('LLL:EXT:ldap_sync_task/locallang.xml:task_error_server')));
			}
			return false;
		}
		
		$start = time();
		
		if (!$this->execSync())
			return false;
		
		$end = time(); 
		if ($this->writeDevLog) { 
			t3lib_div::devLog('LDAP Sync', 'ldap_sync_task', 0, array($GLOBALS['LANG']->sL('LLL:EXT:ldap_sync_task/locallang.xml:task_succes'). ' ' . ($end - $start) . ' seconds'));
		}
		return true;		
	}
	
	public function execSync($dryRun = false) {
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`uid`, `servername`', 
			'`tx_ldapserver`', 
			'`uid` = ' . $this->server . ' AND `hidden` = 0 and `deleted` = 0',
			'',
			1
		);
		
		if (!is_array($records) || empty($records)) {
			if ($this->writeDevLog) { 
				t3lib_div::devLog('LDAP Sync', 'ldap_sync_task', 2, array($GLOBALS['LANG']->sL('LLL:EXT:ldap_sync_task/locallang.xml:task_error_server_notexist')));
			}
			return false;
		}
		
		$objSync = t3lib_div::makeInstance('tx_ldapsync');
		$objSync->writeDevLog = true;
		$objSync->init('', $this->server, $dryRun ? 'simulate' : '');
		$objSync->sync();
		return $objSync->processStatistics();
	}
	
	/**
	 * This method is designed to return some additional information about the task,
	 * that may help to set it apart from other tasks from the same class
	 * This additional information is used - for example - in the Scheduler's BE module
	 * This method should be implemented in most task classes
	 *
	 * @return	string	Information to display
	 */
	public function getAdditionalInformation() {
		if ($this->server) {
			$record = t3lib_BEfunc::getRecord('tx_ldapserver', $this->server);
			if ($record) {
				return t3lib_BEfunc::getRecordTitle('tx_ldapserver', $record) . ' (' . $this->server . ')';
			}
		}
		return parent::getAdditionalInformation();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_sync_task/class.tx_ldapsynctask_task.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_sync_task/class.tx_ldapsynctask_task.php']);
}

?>