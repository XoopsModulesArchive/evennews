<?php
include_once('header.php');
$myts = &MyTextSanitizer::getInstance();
$dirname = $xoopsModule->dirname();

if (isset($_POST['action']))
    $action = $_POST['action'];
else if (isset($_GET['action']))
    $action = $_GET['action'];
else
    $action = '';
// determine proper smarty template for page
switch ($action)
{
    case 'subscribe_conf':
    case 'unsubscribe_conf':
        $xoopsOption['template_main'] = 'evennews_notice.html';
        break;
    case 'subscribe':
        $xoopsOption['template_main'] = 'evennews_subscr.html';
        break;
    case 'unsubscribe':
        $xoopsOption['template_main'] = 'evennews_unsub.html';
        break;
    default:
        $xoopsOption['template_main'] = 'evennews_index.html';
        break;
} 

include_once(XOOPS_ROOT_PATH . '/header.php'); // Include the page header

$xoopsTpl->assign('lang_status', _EN_STATUS);
// Fill smarty variables for each page
switch ($action)
{
    case 'subscribe_conf':
        if ($_POST['user_mail'] == '')
        {
            $xoopsTpl->assign('en_message', _EN_GENERROR);
            break;
        } 

        $ret = addUser(); // Try to add user                
        // Display Appropriate Response to addUser Return value
        switch ($ret)
        {
            case 1: // User Added, Confirmation Sent
                $xoopsTpl->assign('en_message', sprintf(_EN_CONFIRM, $_POST['user_mail']));
                break;
            case 2: // Resending confirmation
                $xoopsTpl->assign('en_message', sprintf(_EN_RESENDCONFIRM, $_POST['user_mail']));
                break;
            case -1: // Email Already Exists
                $xoopsTpl->assign('en_message', _EN_EMAILEXISTS);
                break;
            case -2: // Unable to send mail
                $xoopsTpl->assign('en_message', _EN_EMAILERROR);
                break;
            case -3: // Username Already Exists
                $xoopsTpl->assign('en_message', _EN_USERNAMEEXISTS);
                break;
            case -4: // Username AND Email Exist
                $xoopsTpl->assign('en_message', _EN_USERNAMEEXISTSANDEMAIL);
                break;
            case -5: // No user to confirm
                $xoopsTpl->assign('en_message', _EN_NOUSERTOCONFIRM);
                break;
            case -6: // Unknown Error
                $xoopsTpl->assign('en_message', _EN_UNKNOWNERROR);
                break;
        } 
        break;

    case 'unsubscribe_conf':
        if ($_POST['user_mail'] == '')
        {
            $xoopsTpl->assign('en_message', _EN_GENERROR);
            break;
        } 
        $ret = delUser(); // Try to del user
        switch ($ret)
        {
            case 1: // User Removed
                $xoopsTpl->assign('en_message', sprintf(_EN_REMOVED, $_POST['user_mail']));
                break;
            case 0: // User Unsubscribed, or unconfirmed
            case -1:
                $xoopsTpl->assign('en_message', _EN_REMERROR);
                break;
        } 
        break;
    case 'subscribe':
        global $xoopsModuleConfig;

        $xoopsTpl->assign('en_title', sprintf(_EN_SUBTITLE, $xoopsConfig['sitename']));
        $xoopsTpl->assign('en_form_action', XOOPS_URL . '/modules/' . $dirname . '/index.php');
        $xoopsTpl->assign('EN_DISCLAIMER', sprintf($xoopsModuleConfig['join_text_disclaimer'], $xoopsConfig['sitename'], $xoopsConfig['sitename']));
        $xoopsTpl->assign('en_remote_host', isset($_SERVER['REMOTE_ADDR']));
        $xoopsTpl->assign('lang_emailadress', _EN_EMAIL_ADRESS);
        $xoopsTpl->assign('lang_denote', _EN_DENOTE);
        $xoopsTpl->assign('lang_nickname', _EN_NICKNAME);
        $xoopsTpl->assign('lang_name', _EN_NAME);
        $xoopsTpl->assign('lang_email', _EN_EMAIL_ADRESS);

        $xoopsTpl->assign('lang_submit_button', _EN_SUBMITBTN);
        $xoopsTpl->assign('lang_enter_name', _EN_JS_ERROR1);
        $xoopsTpl->assign('lang_enter_surname', _EN_JS_ERROR2);
        $xoopsTpl->assign('lang_enter_email', _EN_JS_ERROR3);

        $xoopsTpl->assign('lang_emailtype', _EN_EMAIL_TYPE);
        $xoopsTpl->assign('lang_emailtxt', _EN_EMAIL_TEXT);
        $xoopsTpl->assign('lang_emailhtml', _EN_EMAIL_HTML);
        $xoopsTpl->assign('lang_new_user', _EN_NEWUSER);
        $xoopsTpl->assign('lang_email_new_user', _EN_EMAIL_NEWUSER);
        $xoopsTpl->assign('lang_email_confirm', _EN_EMAIL_CONFIRM);

        if ($xoopsUser)
        {
            $xoopsTpl->assign('en_realname', $xoopsUser->getVar('name'));
            $xoopsTpl->assign('en_username', $xoopsUser->getVar('uname'));
            $xoopsTpl->assign('en_email', $xoopsUser->getVar('email'));
        } 
        else
        {
            $xoopsTpl->assign('en_realname', _EN_JS_ERROR1);
            $xoopsTpl->assign('en_username', _EN_JS_ERROR5);
            $xoopsTpl->assign('en_email', _EN_JS_ERROR3);
        } 

        break;
    case 'unsubscribe':
        global $xoopsModuleConfig;

        $xoopsTpl->assign('en_title', sprintf(_EN_UNSUBTITLE, $xoopsConfig['sitename']));
        $xoopsTpl->assign('en_form_action', XOOPS_URL . '/modules/' . $dirname . '/index.php');
        $xoopsTpl->assign('lang_unsubscribe', _EN_BTNUNSUBSCRIBE);
        $xoopsTpl->assign('lang_emailadress', _EN_EMAIL_ADRESS);

        if ($xoopsUser)
        {
            $xoopsTpl->assign('en_email', $xoopsUser->getVar('email'));
        } 
        else
        {
            $xoopsTpl->assign('en_email', _EN_ERROR1);
        } 
        break;
    default:

        global $xoopsUser, $xoopsDB, $xoopsModuleConfig;

        $messages = array();
        $limit_number = $xoopsModuleConfig['num_messages'];

        $sql = "SELECT * FROM " . $xoopsDB->prefix('evennews_messages') . "" ;
        $list = $xoopsDB->getRowsNum($xoopsDB->query($sql));

        $sql2 = "SELECT * FROM " . $xoopsDB->prefix('evennews_messages') . " ORDER BY time_sent DESC LIMIT $limit_number " ;
        $result = $xoopsDB->query($sql2);
        while ($myarray = $xoopsDB->fetchArray($result))
        {
            $messages['mess_id'] = $myts->stripSlashesGPC($myarray['mess_id']);
            $messages['user_id'] = xoops_getLinkedUnameFromId($myarray['user_id']);
            $user = new XoopsUser($myarray['user_id']);
            $messages['user_email'] = $user->email();
            $messages['user_email'] = checkEmail($messages['user_email'], true);
            $messages['time_sent'] = formatTimestamp($myarray['time_sent'], "D d-M-Y");
            $messages['subject'] = $myarray['subject'];
            $messages['message'] = strip_tags(trim($myarray['message']));

            $xoopsTpl->append('messages', $messages);
        } 

        if ($list < $limit_number)
        {
            $limit = $list;
        } 
        else
        {
            $limit = $limit_number;
        } 
        // $xoopsTpl->assign('lang_heading', "Newsletter");
        $xoopsTpl->assign('lang_description_heading', "Description");
        $xoopsTpl->assign('lang_description', $xoopsModuleConfig['description']);
        $xoopsTpl->assign('lang_most_recent_messages', "<b>Most Recent Messages</b> (Showing $limit of $list)");
        $xoopsTpl->assign('lang_message_num', "Msg#");
        $xoopsTpl->assign('lang_total_messages', "Total Messages in Archive: $list");
        $xoopsTpl->assign('lang_view_archive', "View All Messages");
        $xoopsTpl->assign('lang_join_newsletter', "Newsletter Membership");

        $xoopsTpl->assign('lang_to_join', $xoopsModuleConfig['join_text']);
        $xoopsTpl->assign('lang_to_leave', $xoopsModuleConfig['leave_text']);

        $xoopsTpl->assign('lang_tooltip1', _EN_TOOLTIP1);
        $xoopsTpl->assign('lang_tooltip2', _EN_TOOLTIP2);
        $xoopsTpl->assign('lang_heading', $xoopsModule->getVar('name'));
        $xoopsTpl->assign('subscr_url', XOOPS_URL . '/modules/evennews/index.php?action=subscribe');
        $xoopsTpl->assign('unsubscr_url', XOOPS_URL . '/modules/evennews/index.php?action=unsubscribe');
        $xoopsTpl->assign('news_images', sprintf('%s/modules/%s/language/%s/', XOOPS_URL, $dirname, $xoopsConfig['language']));
        unset($messages);
        break;
} 
// Include the page footer
include_once(XOOPS_ROOT_PATH . '/footer.php');

/**
 * ------------------------------------------------------------
 * function delUser() - Removes a user from the newsletter by marking
 * them unconfirmed.
 * -------------------------------------------------------------
 */
function delUser()
{
    global $xoopsDB, $myts, $xoopsConfig, $xoopsModule;

    $query = "SELECT * FROM " . $xoopsDB->prefix('evennews_members') . " WHERE user_email='" . $myts->makeTboxData4Save($_POST['user_mail']) . "' ";
    $result = $xoopsDB->query($query);
    $myarray = $xoopsDB->fetchArray($result);

    $mymail = $myts->makeTboxData4Save($_POST['user_mail']);
    if ($myarray)
    {
        if ($myarray['confirmed'] == '0')
            return -1;

        $query = "UPDATE " . $xoopsDB->prefix('evennews_members') . " SET confirmed='0' WHERE user_email='$mymail'";
        $result = $xoopsDB->queryF($query);
        return 1;
    } 
    else
    {
        return -2;
    } 
} 

/**
 * ------------------------------------------------------------
 * function addUser() - Adds a user to db and sends confirm email
 * -------------------------------------------------------------
 */
function addUser()
{
    global $xoopsDB, $myts, $xoopsConfig, $xoopsModule, $dirname;

    $user_name = $myts->makeTboxData4Save($_POST['user_name']);
    $user_nick = $myts->makeTboxData4Save($_POST['user_nick']);
    $user_mail = $myts->makeTboxData4Save($_POST['user_mail']);
    $user_format = ($_POST['user_format'] == 1) ? 1 : 0;
    $user_host = $myts->makeTboxData4Save($_SERVER['REMOTE_ADDR']);
    
	$query = "SELECT * FROM " . $xoopsDB->prefix('evennews_members') . " WHERE user_email='$user_mail' ";
    $myarray = $xoopsDB->fetchArray($xoopsDB->query($query));

    $query = "SELECT user_nick FROM " . $xoopsDB->prefix('evennews_members') . " WHERE user_nick = '$user_nick'";
    $myarray_name = $xoopsDB->fetchArray($xoopsDB->query($query));

    $xoopsMailer = &getMailer();
    $xoopsMailer->useMail(); 
    // Hervé
    $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/' . $dirname . '/language/' . $xoopsConfig['language'] . '/mail_template');
    $xoopsMailer->setTemplate("confirm_email.tpl");

    if ($_POST['user_confirm'] == 0)
    {
        if ($myarray['user_email'] == $user_mail)
        {
            return -1;
        } 
        if ($myarray_name['user_nick'] == $user_nick)
        {
            return -3;
        } 
    } 
    else
    {
        if ($myarray['user_email'] == $user_mail)
        {
            $confirm_url = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/confirm.php?id=' . $myarray['user_conf'];
            $xoopsMailer->setToEmails($myarray['user_email']);
            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->setSubject(_EN_CONFIRM_SUBJECT);
            $xoopsMailer->assign('X_UNAME', $user_name);
            $xoopsMailer->assign('X_CONTACT_NAME', $xoopsConfig['adminmail']);
            $xoopsMailer->assign('VALIDATION_URL', $confirm_url);
            if ($xoopsMailer->send())
            {
                return 2;
            } 
            else
            {
                return -2;
            } 
        } 
        else
        {
	        return -5;
		} 
    } 

    if (!$myarray && !$myarray_name)
    {
        $time = time();
        $better_token = md5(uniqid(rand(), 1));

        $query = "INSERT INTO " . $xoopsDB->prefix('evennews_members') . " (user_id, user_name, user_nick, user_email, user_host, user_conf, confirmed, activated, user_time, user_html, user_lists  ) ";
        $query .= "VALUES (0, '" . $user_name . "', '" . $user_nick . "', '" . $user_mail . "',
			'" . $user_host . "', '$better_token', '0', '0', '$time', '$user_format', '0')";
        $result = $xoopsDB->queryF($query);
        $error = "Could not create user information: <br /><br />";
        $error .= $query;
        if (!$result)
        {
            trigger_error($error, E_USER_ERROR);
        } 
        $confirm_url = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/confirm.php?id=' . $better_token;
        $xoopsMailer->setToEmails($_POST['user_mail']);
        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        $xoopsMailer->setFromName($xoopsConfig['sitename']);
        $xoopsMailer->setSubject(_EN_CONFIRM_SUBJECT);
        $xoopsMailer->assign('X_UNAME', $user_name);
        $xoopsMailer->assign('X_CONTACT_NAME', $xoopsConfig['adminmail']);
        $xoopsMailer->assign('VALIDATION_URL', $confirm_url);
        if ($xoopsMailer->send())
        {
            return 1;
        } 
        else
        {
            return -2;
        } 
    } 
    else
    {
        return -6;
    } 
} 

?>
