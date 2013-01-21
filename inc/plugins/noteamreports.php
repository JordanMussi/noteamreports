<?php
/***************************************************************************
 *
 *	Author:	Jordan Mussi
 *	File:	./inc/plugins/noteamreports.php
 *  
 *	License:
 *  
 *	This program is free software: you can redistribute it and/or modify it under 
 *	the terms of the GNU General Public License as published by the Free Software 
 *	Foundation, either version 3 of the License, or (at your option) any later 
 *	version.
 *	
 *	This program is distributed in the hope that it will be useful, but WITHOUT ANY 
 *	WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 *	FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License 
 *	for more details.
 *	
 ***************************************************************************/
 
if (!defined('IN_MYBB'))
{
	die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

if(!defined("PLUGINLIBRARY"))
{
	define("PLUGINLIBRARY", MYBB_ROOT."inc/plugins/pluginlibrary.php");
}

$plugins->add_hook('report_error_start', 'noteamreports_run');

function noteamreports_info()
{
	return array(
		'name'          =>  'No Team Reports',
		'description'   =>  'This will stop users reporting all team members posts.',
		'website'       =>  'http://mussi.site90.net/jordan',
		'author'        =>  'Jordan Mussi',
		'authorsite'    =>  'http://mussi.site90.net/jordan',
		'version'       =>  '1',
		'guid'          =>  '5e12d1770a4158fef140f3d5493c4af7',
		'compatibility' =>  '16*',
		);
}

function noteamreports_activate(){
	global $PL;
	if (!file_exists(PLUGINLIBRARY))
	{
		flash_message("The selected plugin could not be activated because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
		admin_redirect("index.php?module=config-plugins");
	}
	$PL or require_once PLUGINLIBRARY;
	//Edit the core to what we want it to be like
	$PL->edit_core('noteamreports', 'report.php',
               array('search'	=>	'$forum = get_forum($post[\'fid\']);',
                     'replace'	=>	'	$plugins_cache = $cache->read(\'plugins\');
if(is_array($plugins_cache) && is_array($plugins_cache[\'active\']) && $plugins_cache[\'active\'][\'noteamreports\'])
{
	if($post[\'uid\'])
{    
    $query = $db->simple_select("users", "usergroup", "uid = \'".$post[\'uid\']."\'");
    while($user = $db->fetch_array($query))
    {
        $usergroup = $cache->read("usergroups");
        if($usergroup[$user[\'usergroup\']][\'showforumteam\'] == "1")
        {
            $error = "You can\'t report a team member\'s post.";
            eval("\$report_error = \"".$templates->get("report_error")."\";");
            output_page($report_error);
            exit;
        }
    }
}
}
$forum = get_forum($post[\'fid\']);'),
               true);
}

function noteamreports_deactive(){
	global $PL;
	
	if (!file_exists(PLUGINLIBRARY))
	{
		flash_message("The selected plugin could not be deactivated because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
		admin_redirect("index.php?module=config-plugins");
	}

	$PL or require_once PLUGINLIBRARY;
	
	//Restore core edits we've done in activation process
	$PL->edit_core('noteamreports', 'report.php',
               array('search'	=>	'if($post[\'uid\'])
{    
    $query = $db->simple_select("users", "usergroup", "uid = \'".$post[\'uid\']."\'");
    while($user = $db->fetch_array($query))
    {
        $usergroup = $cache->read("usergroups");
        if($usergroup[$user[\'usergroup\']][\'showforumteam\'] == "1")
        {
            $error = "You can\'t report a team member\'s post.";
            eval("\$report_error = \"".$templates->get("report_error")."\";");
            output_page($report_error);
            exit;
        }
    }
}',
                     'replace'	=>	''),
               true);
}
?>