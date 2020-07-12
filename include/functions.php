<?php

/**
 * Start of User Functions here
 */
if (isset($_POST['action']))
    $action = $_POST['action'];
else if (isset($_GET['action']) && !isset($_POST['action']))
    $action = $_GET['action'];
else
    $action = '';

function is_error($message)
{
    echo "ERROR:";
	$error = "Could not retrive information from the database: <br /><br />" . $message;
	trigger_error($error, E_USER_ERROR);
}


/**
 * Type: Private
 * 
 * Import Xoops Users into mailing list database.
 */
function launchimport()
{
    global $xoopsDB, $xoopsUser;
    xoops_cp_header();

    evadminmenu(_ADM_EVENNEWS_ADMINMENU);
    $imported = 0;
    while (list($null, $userid) = each($_POST["userslist"]))
    {
        $sql = "SELECT count(user_id) as cpt from " . $xoopsDB->prefix('evennews_members') . " WHERE user_id=$userid";
        $arr = $xoopsDB->fetchArray($xoopsDB->query($sql));
        if ($arr['cpt'] == 0) // The user is not in the table
            {
                // Search user
                $date = time();
				$sqluser = "SELECT name, uname, user_regdate, email, user_mailok FROM " . $xoopsDB->prefix("users") . " WHERE uid= $userid";
            	$arruser = $xoopsDB->fetchArray($xoopsDB->queryF($sqluser));
            	if (trim($arruser['email'] != ''))
            	{
                	if ($arruser['user_mailok'] == 1) // User accepts emails
                    {
                        $date = time();
						$better_token = md5(uniqid(rand(), 1));
                    	$sqlinsert = sprintf("INSERT INTO %s (user_id, user_name, user_nick, user_email, user_host, user_conf, confirmed, activated, user_time) VALUES (%u ,'%s' ,'%s', '%s', '%s', '%s', '1', '1' , '%s')", $xoopsDB->prefix('evennews_members'), $userid, $arruser['name'], $arruser['uname'], $arruser['email'], '', $better_token, $date);
                    	if (!$resultinsert = $xoopsDB->queryF($sqlinsert))
                    	{
                        	printf(_ADM_EVENNEWS_USERSMSG5, $xoopsUser->getUnameFromId($userid));
                    	}
                    	else // User inserted successfully
                        {
                            printf(_ADM_EVENNEWS_USERSMSG4, $xoopsUser->getUnameFromId($userid));
                    	}
                }
                else
                {
                    printf(_ADM_EVENNEWS_USERSMSG3, $xoopsUser->getUnameFromId($userid));
                }
            }
            else // Empty email adress
                {
                    printf(_ADM_EVENNEWS_USERSMSG2, $xoopsUser->getUnameFromId($userid));
            }
        }
        else // User already present in the table
            {
                printf(_ADM_EVENNEWS_USERSMSG1, $xoopsUser->getUnameFromId($userid));
        }
    }

    xoops_cp_footer();
}

/**
 * Type: Private
 * 
 * Removes/Deletes a user.
 */
function removeUser()
{
    global $xoopsDB, $adminURL;

    $sqluser = "SELECT * from " . $xoopsDB->prefix('evennews_members') . " WHERE user_id =" . $_GET['user_id'] . "";
    $arruser = $xoopsDB->fetchArray($xoopsDB->queryF($sqluser));

    $arruser['user_name'] = (!empty($arruser['user_name'])) ? $arruser['user_name'] : $arruser['user_nick'];

    if ($arruser['activated'] == 0)
    {
        define("_SAVE", "Save");
        xoops_cp_header();
        evadminmenu(_ADM_EVENNEWS_ADMINMENU);
        echo "<form action='" . xoops_getenv('PHP_SELF') . "' method='post'>\n";
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>Notice:</legend>";
        echo "<div align = \"cente\" style=\"padding: 8px;\"><b>User <b>" . $arruser['user_name'] . "</b> has already been unsubscribed for the mailing list.</b></div>";
        
        echo "<center><table cellpadding='2' cellspacing='1' class = \"outer\">\n";
        echo "<tr>
				<td class = \"head\">Do you wish to re-activate user <b>" . $arruser['user_name'] . "?</b> </td>
				<td class = \"even\"> <input type='radio' name='activate' value='1'> " . _YES . " &nbsp;
					<input type='radio' name='activate' value='0' checked> " . _NO . "
				</td>
			</tr>\n";
        echo "<input type='hidden' name='action' value='reactivate'>\n";
        echo "<input type='hidden' name='user_id' value='" . $arruser['user_id'] . "'>\n";
        echo "<tr>
				<td class = \"even\">&nbsp;</td>\n";
        echo "<td colspan = \"2\" class = \"even\"><input type='submit' name='Submit' value='" . _SAVE . "'></td></tr>\n";
        echo "</table></center><br />";
        echo "</fieldset>";
        echo "</form>\n";
        xoops_cp_footer();
        exit();
    } 
    // }
    $sql = "UPDATE " . $xoopsDB->prefix('evennews_members') . " SET activated ='0' WHERE user_id =" . $_GET['user_id'] . "";
    $result = $xoopsDB->queryF($sql);
    $error = "" . _ADM_EVENNEWS_DBERROR . ": <br /><br />" . $sql;

    if (!$result)
    {
        trigger_error($error, E_USER_ERROR);
    }
    redirect_header(xoops_getenv('PHP_SELF'), 2, sprintf(_ADM_EVENNEWS_USERREMOVED, $arruser['user_name']));
}

function delUnconf()
{
    global $xoopsDB;

    $user_id = (isset($_POST['user_id'])) ? $_POST['user_id']: $_GET['user_id'];

    if ($user_id != 0)
    {
        if (!isset($_POST['confirm']))
        {
            xoops_cp_header();
            echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _ADM_CONFDELETE . "</legend><br />"; 
            // echo "<h4>" . _ADM_CONFDELETE . "</h4>";
            xoops_confirm(array('action' => 'clean_unconf', 'user_id' => $user_id, 'confirm' => 1), 'user.php?action=clean_unconf', _ADM_EVENNEWS_WARNING);
            echo "<br /></fieldset>";
            xoops_cp_footer();
            exit();
        }
        else
        {
            $sql = "DELETE FROM " . $xoopsDB->prefix('evennews_members') . " WHERE user_id='" . $user_id . "'";
            $result = $xoopsDB->query($sql);
            $error = "Error while deleting user Data: <br /><br />" . $sql;
            $message = "This member has been deleted";
        }
    }
    else
    {
        $sql = "DELETE FROM " . $xoopsDB->prefix('evennews_members') . " WHERE confirmed ='0' and activated ='0'";
        $result = $xoopsDB->query($sql);
        $list = $xoopsDB->getRowsNum($sql);
        $error = "Error while deleting unconfirmed user Data: <br /><br />" . $sql;
        $message = sprintf(_ADM_EVENNEWS_DELETED, $list);
    }

    if (!$result)
    {
        if (!$error)
        {
            $message = sprintf(_ADM_EVENNEWS_DELETED, "No");
        }
        else
        {
            trigger_error($error, E_USER_ERROR);
        }
    }
    redirect_header(xoops_getenv('PHP_SELF'), 1, $message);
}

function addUser()
{
    global $xoopsDB, $xoopsUser;

    if (checkEmail($_POST['user_mail']) == FALSE)
    {
        redirect_header("" . xoops_getenv('PHP_SELF') . "?action=add_user", 2, "Email Address entered is Invalid");
    }

    $better_token = md5(uniqid(rand(), 1));
    if ($_POST['user_id'])
    {
		$user_format = ($_POST['user_format'] == 1) ? 1 : 0;
		$activated = ($_POST['activated'] == 1) ? 1 : 0;
		$confirmed = ($_POST['confirmed'] == 1) ? 1 : 0;

		$query = "UPDATE " . $xoopsDB->prefix('evennews_members') . " SET user_name = '" . $_POST['user_name'] . "', user_nick ='" . $_POST['user_nick'] . "', user_email ='" . $_POST['user_mail'] . "', confirmed='$confirmed', activated='$activated', user_html = '$user_format', user_lists = '0' WHERE user_id =" . $_POST['user_id'] . "";
        $error = "Could not update user information: <br /><br />";
        $error .= $query;
        $saveinfo = _ADM_EVENNEWS_USERUPDATED;

    }
    else
    {
        $date = time();
		$activated = ($_POST['confirmed'] == 1) ? 1 : 0;
		$user_format = ($_POST['user_format'] == 1) ? 1 : 0;

		$query = "INSERT INTO " . $xoopsDB->prefix('evennews_members') . " (user_id, user_name, user_nick, user_email, user_host, user_conf, confirmed, activated, user_time, user_html, user_lists  ) ";
        $query .= "VALUES (0, '" . $_POST['user_name'] . "', '" . $_POST['user_nick'] . "', '" . $_POST['user_mail']."',
			'" . $_POST['user_host'] . "', '$better_token', '1', '$activated', '$date', '$user_format', '')";
        $error = "Could not create user information: <br /><br />";
        $error .= $query;
        $saveinfo = _ADM_EVENNEWS_USERADDED;
    }
    $result = $xoopsDB->queryF($query);
    if (!$result)
    {
		trigger_error($error, E_USER_ERROR);
    }
    redirect_header("" . xoops_getenv('PHP_SELF') . "?action=rem_user", 2, sprintf($saveinfo, $_POST['user_name']));
}

function reactivate()
{
    global $xoopsDB, $xoopsUser;

	$sqluser = "SELECT * from " . $xoopsDB->prefix('evennews_members') . " WHERE user_id =" . $_POST['user_id'] . "";
    $arruser = $xoopsDB->fetchArray($xoopsDB->queryF($sqluser));

    $date = time();
	$query = "UPDATE " . $xoopsDB->prefix('evennews_members') . " SET activated='1', user_time = '$date' WHERE user_id =" . $_POST['user_id'] . "";
    $error = "Could not update user information: <br /><br />";
    $error .= $query;
    $saveinfo = _ADM_EVENNEWS_USERADDED;

    $result = $xoopsDB->queryF($query);
    if (!$result)
    {
        trigger_error($error, E_USER_ERROR);
    }
    redirect_header("" . xoops_getenv('PHP_SELF') . "?action=rem_user", 2, sprintf("User %s has been re-activated", $arruser['user_name']));
}

/**
 * End of User functions here
 */

/**
 * adminmenu()
 * 
 * @param string $header optional : You can gice the menu a nice header
 * @param string $extra optional : You can gice the menu a nice footer
 * @param array $menu required : This is an array of links. U can
 * @param int $scount required : This will difine the amount of cells long the menu will have.  
 * NB: using a value of 3 at the moment will break the menu where the cell colours will be off display.
 * @return 
 */

function evadminmenu($header = '', $extra = '', $menu = '', $scount = 4)
{
    global $xoopsConfig, $xoopsModule, $adminURL;

    if (empty($menu))
    {
        /**
         * You can change this part to suit your own module. Defining this here will save you form having to do this each time.
         */ 
        // _AM_WFS_ADMENU1 => "" . XOOPS_URL . "/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule -> getVar('mid') . "",
        $menu = array(
            _ADM_EVENNEWS_MODULECONFIG => "" . XOOPS_URL . "/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "",
            _ADM_EVENNEWS_DEFAULTPAGE => "index.php",
            _ADM_EVENNEWS_SENDMESSAGE => "index.php?action=send",
            _ADM_EVENNEWS_VIEWARCHIV => "index.php?action=view_archives",
            _ADM_EVENNEWS_LISTSUBSCR => "user.php",
            _ADM_EVENNEWS_ADDUSER => "user.php?action=add_user",
            _ADM_EVENNEWS_IMPORTUSER => "user.php?action=import_users",
            _ADM_EVENNEWS_OPTIMDATAB => "index.php?action=optimize",
            );
    }
    /**
     * the amount of cells per menu row
     */
    $count = 0;
    /**
     * Set up the first class
     */
    $class = "even";
    /**
     * Sets up the width of each menu cell
     */
    $width = 100 / $scount;

    /**
     * Menu table begin
     */
    //echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . $header . "</legend><br />";
    echo "<table width = '100%' cellpadding= '2' cellspacing= '1' class='outer'><tr>";

    /**
     * Check to see if $menu is and array
     */
    if (is_array($menu))
    {
        foreach ($menu as $menutitle => $menulink)
        {
            $count++;
            echo "<td class='$class' align='center' valign='middle' width= $width%>";
            echo "<a href='" . $menulink . "'>" . $menutitle . "</a></td>";

            /**
             * Break menu cells to start a new row if $count > $scount
             */
            if ($count == $scount)
            {
                /**
                 * If $class is the same for the end and start cells, invert $class
                 */
                $class = ($class == 'odd') ? "odd" : "even";
                echo "</tr>";
                $count = 0;
            }
            else
            {
                $class = ($class == 'even') ? "odd" : "even";
            }
        }
        /**
         * checks to see if there are enough cell to fill menu row, if not add empty cells
         */
        if ($count >= 1)
        {
            $counter = 0;
            while ($counter < $scount - $count)
            {
                echo '<td class="' . $class . '">&nbsp;</td>';
                $class = ($class == 'even') ? 'odd' : 'even';
                $counter++;
            }
        }
        echo "</table><br />";
        //echo "</fieldset>";
    }
    if ($extra)
    {
        echo "<div><h4>$extra</h4></div>";
    }
}

function select_date()
{
    echo "<div>";
    echo "" . _ADM_DAYC . " <select name='autoday'>";
    $autoday = date('d');
    for ($xday = 1; $xday < 32; $xday++)
    {
        $sel = ($xday == $autoday) ? 'selected="selected"' : '';
        echo "<option value='$xday' $sel>$xday</option>";
    }
    echo "</select>&nbsp;";

    echo _ADM_MONTH . " <select name='automonth'>";
    $automonth = date('m');
    for ($xmonth = 1; $xmonth < 13; $xmonth++)
    {
        $sel = ($xmonth == $automonth) ? 'selected="selected"' : '';
        echo "<option value='$xmonth' $sel>$xmonth</option>";
    }
    echo "</select>&nbsp;";

    echo _ADM_YEAR . " <select name='autoyear'>";
    $autoyear = date('Y');
    $cyear = date('Y');
    for ($xyear = ($autoyear-1); $xyear < ($cyear + 7); $xyear++)
    {
        $sel = ($xyear == $autoyear) ? 'selected="selected"' : '';
        echo "<option value='$xyear' $sel>$xyear</option>";
    }
    echo "</select>";
    echo "</div>";
}
/**
 * Image defines from here
 */
$editimg = "<img src=" . XOOPS_URL . "/modules/" . DIR_NAME . "/images/icon/edit.gif ALT=''>";
$deleteimg = "<img src=" . XOOPS_URL . "/modules/" . DIR_NAME . "/images/icon/delete.gif ALT=''>";
$approve = "<img src=" . XOOPS_URL . "/modules/" . DIR_NAME . "/images/icon/approve.gif ALT=''>";
$viewimg = "<img src=" . XOOPS_URL . "/modules/" . DIR_NAME . "/images/icon/view.gif ALT=''>";

function showuser($showuser)
{
    global $xoopsDB, $xoopsConfig;
    $ret = "<select size='1' name='showuser' onchange='location.href=\"user.php?showuser=\"+this.options[this.selectedIndex].value'>";

    $usertype = array("All Users" => 0 , "Confirmed Users" => 1, "Un-Activated Users" => 2);
    $user = 0;
    foreach ($usertype as $usertypes => $names)
    {
        if ($showuser == $user)
        {
            $opt_selected = "selected='selected'";
        }
        else
        {
            $opt_selected = "";
        }
        $ret .= "<option value='" . $user . "' $opt_selected>" . $usertypes . "</option>";
        $user++;
    }
    $ret .= "</select>";
    return $ret;
}

function showtype($showtype)
{
    global $xoopsDB, $xoopsConfig;
    $ret = "<select size='1' name='showtype' onchange='location.href=\"user.php?showtype=\"+this.options[this.selectedIndex].value'>";

    $listtype = array("Name" => 0, "Name" => 1, "Nick Name" => 2, "Nick Email" => 3, "Date Subscribed" => 4);

    $lists = 0;
    foreach ($listtype as $listtypes => $name)
    {
        if ($showtype == $lists)
        {
            $opt_selected = "selected='selected'";
        }
        else
        {
            $opt_selected = "";
        }
        $ret .= "<option value='" . $name . "' $opt_selected>" . $listtypes . "</option>";
        $lists++;
    }
    $ret .= "</select>";
    return $ret;
}

function showlist($showorder)
{
    global $xoopsDB, $xoopsConfig;
    $ret = "<select size='1' name='showorder' onchange='location.href=\"user.php?showorder=\"+this.options[this.selectedIndex].value'>";

    $listing = array("Accending" => 0, "Descending" => 1);

    $orders = 0;
    foreach ($listing as $listings => $ord)
    {
        if ($showorder == $orders)
        {
            $opt_selected = "selected='selected'";
        }
        else
        {
            $opt_selected = "";
        }
        $ret .= "<option value='" . $ord . "' $opt_selected>" . $listings . "</option>";
        $orders++;
    }
    $ret .= "</select>";
    return $ret;
}

?>
