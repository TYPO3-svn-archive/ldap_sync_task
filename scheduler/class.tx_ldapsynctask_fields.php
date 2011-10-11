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
 * Adds additional fields for the IndexMaintenance task.
 *
 * @author	Prud'homme Emilie <emilie@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_ldapsyn_task
 */
class tx_ldapsynctask_fields implements tx_scheduler_AdditionalFieldProvider {
	/**
	 * Used to define the additional fields
	 *
	 * @param	array					$aTaskInfo: reference to the array containing the info used in the add/edit form
	 * @param	tx_scheduler_Task		$oTask: when editing, reference to the current task object. Null when adding.
	 * @param	tx_scheduler_module1	$oSchedulerModule: reference to the calling object (Scheduler's BE module)
	 * @return	array					Array containg all the information pertaining to the additional fields
	 *									The array is multidimensional, keyed to the task class name and each field's id
	 *									For each field it provides an associative sub-array with the following:
	 */
	public function getAdditionalFields(array &$aTaskInfo, $oTask, tx_scheduler_Module $oSchedulerModule) {
		$aAdditionalFields = array();
				
		$servers = $this->getRecordsServer();
		$sFieldId = 'ldap_server';
		$sFieldHtml = '';
		$sFieldHtml .= '<select name="tx_scheduler[server]" id="' . $sFieldId . '">
				<option value="0"></option>';
		
		if (!is_array($servers) || empty($servers)) {
			$oSchedulerModule->addMessage($GLOBALS['LANG']->sL('LLL:EXT:ldap_sync_task/locallang.xml:error_create_server'));
		} else {
			foreach ($servers as $server) {
				$selected = '';
				if ($oSchedulerModule->CMD == 'edit' && $server['uid'] == $oTask->server)
					$selected = 'selected="selected"';
				$sFieldHtml .= '<option value="' . $server['uid'] . '" ' . $selected . '>' . $server['servername'] . ' (' . $server['uid'] . ')</option>';
			}
		}

		$sFieldHtml .= '</select>';
		
		$aAdditionalFields[$sFieldId] = array(
			'code'     => $sFieldHtml,
			'label'    => 'LLL:EXT:ldap_sync_task/locallang.xml:scheduler_field_server',
			'cshKey'   => '',
			'cshLabel' => $sFieldId
		);
		
		return $aAdditionalFields;
	}

	/**
	 * Checks any additional data that is relevant to this task. If the task
	 * class is not relevant, the method is expected to return true
	 *
	 * @param	array					$aSubmittedData: reference to the array containing the data submitted by the user
	 * @param	tx_scheduler_module1	$parentObject: reference to the calling object (Scheduler's BE module)
	 * @return	boolean					True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$aSubmittedData, tx_scheduler_Module $oSchedulerModule) {
		global $LANG;
		$bResult = true;
			
		if (!$aSubmittedData['server']) {
			$oSchedulerModule->addMessage($GLOBALS['LANG']->sL('LLL:EXT:ldap_sync_task/locallang.xml:error_server'));
			$bResult = false;
		} else {
			$servers = $this->getRecordsServer($aSubmittedData['server']);
			if (!is_array($servers) || empty($servers)) {
				$oSchedulerModule->addMessage($GLOBALS['LANG']->sL('LLL:EXT:ldap_sync_task/locallang.xml:error_server'));
				$bResult = false;
			}
		}
		
		return $bResult;
	}

	/**
	 * Saves any additional input into the current task object if the task
	 * class matches.
	 *
	 * @param	array				$aSubmittedData: array containing the data submitted by the user
	 * @param	tx_scheduler_Task	$oTask: reference to the current task object
	 */
	public function saveAdditionalFields(array $pi_submittedData, tx_scheduler_Task $oTask) {
		$oTask->server = $pi_submittedData['server'];
	}
	
	/**
	 * Get configuration server records
	 *
	 * @param	int	$uid: restriction by uid
	 */
	function getRecordsServer($uid = 0) {
		$addWhere = '';
		if ($uid) 
			$addWhere .= ' AND `uid` = ' . $uid;
		
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'`uid`, `servername`', 
			'`tx_ldapserver`', 
			'`hidden` = 0 and `deleted` = 0 ' . $addWhere
		);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_sync_task/class.tx_ldapsynctask_fields.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_sync_task/class.tx_ldapsynctask_fields.php']);
}

?>