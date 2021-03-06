<?php
// Any copyright notice, instructions, etc...
$modversion['name'] = _MI_EVENNEWS_NAME;
$modversion['version'] = 2.1;
$modversion['description'] = _MI_EVENNEWS_DESC;
$modversion['credits'] = 'Original coding by Sean Holt [mordist@cox.net] ';
$modversion['author'] = 'Xoops Port By Brian Wahoff, modified by Herv� Thouzard and Updated By Catzwolf ';
$modversion['help'] = 'help.html';
$modversion['license'] = 'GPL see LICENSE';
$modversion['official'] = 0;
$modversion['image'] = 'images/logo.png';
$modversion['dirname'] = 'evennews';

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminmenu'] = 'admin/menu.php';
$modversion['adminindex'] = "admin/index.php";

// Menu
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name'] = _MI_EVENNEWS_VIEWARCHIVE;
$modversion['sub'][1]['url'] = "archives.php";

// Search
$modversion['hasSearch'] = 0;
$modversion['search']['file'] = "include/search.inc.php";
$modversion['search']['func'] = "even_search";

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "evennews_maillists";
$modversion['tables'][1] = "evennews_members";
$modversion['tables'][2] = "evennews_messages";

// Templates
$modversion['templates'][1]['file'] = 'evennews_index.html';
$modversion['templates'][1]['description'] = _MI_EVENNEWS_TEMPINDEX;
$modversion['templates'][2]['file'] = 'evennews_subscr.html';
$modversion['templates'][2]['description'] = _MI_EVENNEWS_TEMPSUBSC;
$modversion['templates'][3]['file'] = 'evennews_unsub.html';
$modversion['templates'][3]['description'] = _MI_EVENNEWS_TEMPUNSUB;
$modversion['templates'][4]['file'] = 'evennews_notice.html';
$modversion['templates'][4]['description'] = _MI_EVENNEWS_TEMPNOTICE;
$modversion['templates'][5]['file'] = 'evennews_archive.html';
$modversion['templates'][5]['description'] = _MI_EVENNEWS_TEMPARCHIVE;
$modversion['templates'][6]['file'] = 'evennews_message.html';
$modversion['templates'][6]['description'] = _MI_EVENNEWS_TEMPMESSAGE;

// Blocks
$modversion['blocks'][1]['file'] = "evennews_blocks.php";
$modversion['blocks'][1]['name'] = _MI_EVENNEWS_BNAME1;
$modversion['blocks'][1]['description'] = "Allow users to subscribe or unsubscribe";
$modversion['blocks'][1]['show_func'] = "b_evennews_subscr";
$modversion['blocks'][1]['options'] = "";
$modversion['blocks'][1]['edit_func'] = "";
$modversion['blocks'][1]['template'] = 'evennews_block_subcr.html';

//config
$modversion['config'][1]['name'] = 'systememail';
$modversion['config'][1]['title'] = '_MI_EVENNEWS_SYSTEMEMAIL';
$modversion['config'][1]['description'] = '_MI_EVENNEWS_SYSTEMEMAILDSC';
$modversion['config'][1]['formtype'] = 'textbox';
$modversion['config'][1]['valuetype'] = 'text';
$modversion['config'][1]['default'] = '';

$modversion['config'][2]['name'] = 'num_messages';
$modversion['config'][2]['title'] = '_MI_EVENNEWS_NUMMESSAGES';
$modversion['config'][2]['description'] = '_MI_EVENNEWS_NUMMESSAGESDSC';
$modversion['config'][2]['formtype'] = 'select';
$modversion['config'][2]['valuetype'] = 'int';
$modversion['config'][2]['default'] = 5;
$modversion['config'][2]['options'] = array( '5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50 );

$modversion['config'][3]['name'] = 'description';
$modversion['config'][3]['title'] = '_MI_EVENNEWS_DESCRIPTION';
$modversion['config'][3]['description'] = '_MI_EVENNEWS_DESCRIPTIONDSC';
$modversion['config'][3]['formtype'] = 'textarea';
$modversion['config'][3]['valuetype'] = 'text';
$modversion['config'][3]['default'] = "Welcome to our News letter, you can keep up-to-date with the goings on of our website with out Newsletter.";

$modversion['config'][4]['name'] = 'join_text';
$modversion['config'][4]['title'] = '_MI_EVENNEWS_EVENNEWS_JOIN';
$modversion['config'][4]['description'] = '_MI_EVENNEWS_EVENNEWS_JOINDSC';
$modversion['config'][4]['formtype'] = 'textarea';
$modversion['config'][4]['valuetype'] = 'text';
$modversion['config'][4]['default'] = "If you would like to subscribe to our news letter please use this link.";

$modversion['config'][5]['name'] = 'leave_text';
$modversion['config'][5]['title'] = '_MI_EVENNEWS_LEAVE';
$modversion['config'][5]['description'] = '_MI_EVENNEWS_LEAVEDSC';
$modversion['config'][5]['formtype'] = 'textarea';
$modversion['config'][5]['valuetype'] = 'text';
$modversion['config'][5]['default'] = "If you would like to unsubscribe from our news letter please use this link";

$modversion['config'][6]['name'] = 'join_text_disclaimer';
$modversion['config'][6]['title'] = '_MI_EVENNEWS_LEAVE';
$modversion['config'][6]['description'] = '_MI_EVENNEWS_LEAVEDSC';
$modversion['config'][6]['formtype'] = 'textarea';
$modversion['config'][6]['valuetype'] = 'text';
$modversion['config'][6]['default'] = "We at XOOPS Site value your privacy, we will never sell or distribute for compensation your information. You will only receive information regarding XOOPS Site or its affiliates. <br /><br />By joining the list you agree not to freak out if you receive an e-mail or two from us per week, sometimes less, sometimes more. At anytime you may remove yourself from the list if you think you joined in error. ";

$modversion['config'][7]['name'] = 'autoapprove';
$modversion['config'][7]['title'] = '_MI_SUBSCRIBE_LEAVE';
$modversion['config'][7]['description'] = '_MI_SUBSCRIBE_LEAVEDSC';
$modversion['config'][7]['formtype'] = 'yesno';
$modversion['config'][7]['valuetype'] = 'int';
$modversion['config'][7]['default'] = 0;

$modversion['config'][8]['name'] = 'usespaw';
$modversion['config'][8]['title'] = '_MI_EVENNEWS_WYSIWYG';
$modversion['config'][8]['description'] = '_MI_EVENNEWS_WYSIWYGDSC';
$modversion['config'][8]['formtype'] = 'yesno';
$modversion['config'][8]['valuetype'] = 'int';
$modversion['config'][8]['default'] = 0;
?>