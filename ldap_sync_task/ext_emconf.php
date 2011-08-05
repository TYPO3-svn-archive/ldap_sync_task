<?php

########################################################################
# Extension Manager/Repository config file for ext "ldap_sync_task".
#
# Auto generated 05-08-2011 15:08
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'LDAP Synchronisation Task',
	'description' => 'Add scheduler task to automate LDAP synchronisation, depend of ldap_sync extension',
	'category' => 'be',
	'author' => 'In Cite Solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => 'ldap_sync',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'ldap_sync' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:11:{s:9:"ChangeLog";s:4:"9229";s:10:"README.txt";s:4:"ee2d";s:32:"class.tx_ldapsynctask_fields.php";s:4:"f348";s:30:"class.tx_ldapsynctask_task.php";s:4:"0ecf";s:16:"ext_autoload.php";s:4:"b199";s:12:"ext_icon.gif";s:4:"0898";s:17:"ext_localconf.php";s:4:"0295";s:13:"locallang.xml";s:4:"c724";s:24:"tx_ldapsynctask_task.php";s:4:"ce44";s:19:"doc/wizard_form.dat";s:4:"c860";s:20:"doc/wizard_form.html";s:4:"3457";}',
	'suggests' => array(
	),
);

?>