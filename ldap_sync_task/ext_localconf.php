
<?php
if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

// SEDIT
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_ldapsynctask_task'] = array(
    'extension'        => $_EXTKEY,
    'title'            => 'LDAP Synchronisation',
    'description'      => 'LDAP Synchronisation',
    'additionalFields' => 'tx_ldapsynctask_fields'
);

$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys']['ldap_sync_task'] = array('EXT:ldap_sync_task/tx_ldapsynctask_task.php','_CLI_scheduler');

?>

