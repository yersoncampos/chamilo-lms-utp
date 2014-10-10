<?php
/* For licensing terms, see /license.txt */
/**
*	@author Bart Mollet, Julio Montoya lot of fixes
*	@package chamilo.admin
*/
/*		INIT SECTION */

// name of the language file that needs to be included
$language_file = 'admin';
$cidReset = true;
require_once '../../inc/global.inc.php';

if (!api_is_course_manager_admin()) {
    api_not_allowed();
}

// setting the section (for the tabs)
$this_section = SECTION_PLATFORM_ADMIN;

$id_session = (int)$_GET['id_session'];
$courseCode = $_GET['course'];
if (empty($courseCode)) {
    api_not_allowed(true);
}

api_protect_course_admin_manager($courseCode);

//SessionManager::protect_session_edit($id_session);

$tool_name = get_lang('SessionOverview');

$interbreadcrumb[] = array('url' => 'index.php','name' => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array('url' => 'session_list.php','name' => get_lang('SessionList'));

$session = api_get_session_info($id_session);
$session_cat_info = SessionManager::get_session_category($session['session_category_id']);
$session_category = null;
if (!empty($session_cat_info)) {
    $session_category = $session_cat_info['name'];
}

$action = isset($_GET['action']) ? $_GET['action'] : null;

$url_id = api_get_current_access_url_id();
/*
switch ($action) {
    case 'add_user_to_url':
        $user_id = $_REQUEST['user_id'];
        $result = UrlManager::add_user_to_url($user_id, $url_id);
        $user_info = api_get_user_info($user_id);
        if ($result) {
            $message = Display::return_message(get_lang('UserAdded').' '.api_get_person_name($user_info['firstname'], $user_info['lastname']), 'confirm');
        }
        break;
    case 'delete':
        if (isset($_GET['course_code_to_delete'])) {
            SessionManager::delete_course_in_session($id_session, $_GET['course_code_to_delete']);
        }
        if (!empty($_GET['user'])) {
            SessionManager::unsubscribe_user_from_session($id_session, $_GET['user']);
        }
        break;
}*/

Display::display_header($tool_name);

if (!empty($_GET['warn'])) {
    Display::display_warning_message(urldecode($_GET['warn']));
}

if (!empty($message)) {
    echo $message;
}
echo Display::page_header(Display::return_icon('session.png', get_lang('Session')).' '.$session['name']." <small>$dates</small>");

$url = Display::url(Display::return_icon('edit.png', get_lang('Edit'), array(), ICON_SIZE_SMALL), "session_add.php?page=resume_session.php&id=$id_session");
$url = null;
echo Display::page_subheader(get_lang('GeneralProperties').$url);

$coach_info = api_get_user_info($session['id_coach']);

?>
    <!-- General properties -->
    <table class="data_table">
        <tr>
            <td><?php echo get_lang('GeneralCoach'); ?> :</td>
            <td><?php echo api_get_person_name($session['firstname'], $session['lastname']).' ('.$session['username'].')' ?></td>
        </tr>
        <?php if(!empty($session_category)) { ?>
            <tr>
                <td><?php echo get_lang('SessionCategory') ?></td>
                <td><?php echo $session_category;  ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td><?php echo get_lang('Date'); ?> :</td>
            <td>
                <?php
                if ($session['date_start'] == '00-00-0000' && $session['date_end']== '00-00-0000' )
                    echo get_lang('NoTimeLimits');
                else {
                    if ($session['date_start'] != '00-00-0000') {
                        //$session['date_start'] = Display::tag('i', get_lang('NoTimeLimits'));
                        $session['date_start'] =  get_lang('From').' '.$session['date_start'];
                    } else {
                        $session['date_start'] = '';
                    }
                    if ($session['date_end'] == '00-00-0000') {
                        $session['date_end'] ='';
                    } else {
                        $session['date_end'] = get_lang('Until').' '.$session['date_end'];
                    }
                    echo $session['date_start'].' '.$session['date_end'];
                }
                ?>
            </td>
        </tr>
        <!-- show nb_days_before and nb_days_after only if they are different from 0 -->
        <tr>
            <td>
                <?php echo api_ucfirst(get_lang('DaysBefore')) ?> :
            </td>
            <td>
                <?php echo intval($session['nb_days_access_before_beginning']) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo api_ucfirst(get_lang('DaysAfter')) ?> :
            </td>
            <td>
                <?php echo intval($session['nb_days_access_after_end']) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo api_ucfirst(get_lang('SessionVisibility')) ?> :
            </td>
            <td>
                <?php if ($session['visibility']==1) echo get_lang('ReadOnly'); elseif($session['visibility']==2) echo get_lang('Visible');elseif($session['visibility']==3) echo api_ucfirst(get_lang('Invisible'))  ?>
            </td>
        </tr>


<?php



$multiple_url_is_on = api_is_multiple_url_enabled();

if ($multiple_url_is_on) {
    echo '<tr><td>';
    echo 'URL';
    echo '</td>';
    echo '<td>';
    $url_list = UrlManager::get_access_url_from_session($id_session);
    foreach ($url_list as $url_data) {
        echo $url_data['url'].'<br />';
    }
    echo '</td></tr>';
}
?>
</table>
<br />

<?php

$url = Display::url(Display::return_icon('edit.png', get_lang('Edit'), array(), ICON_SIZE_SMALL), "add_courses_to_session.php?page=resume_session.php&id_session=$id_session");
$url = null;
echo Display::page_subheader(get_lang('CourseList').$url);

?>

<!--List of courses -->
<table class="data_table">
<tr>
  <th width="35%"><?php echo get_lang('CourseTitle'); ?></th>
  <th width="30%"><?php echo get_lang('CourseCoach'); ?></th>
  <th width="20%"><?php echo get_lang('UsersNumber'); ?></th>
  <th width="15%"><?php echo get_lang('Actions'); ?></th>
</tr>
<?php
if ($session['nbr_courses'] == 0){
	echo '<tr>
			<td colspan="4">'.get_lang('NoCoursesForThisSession').'</td>
		</tr>';
} else {
    $courses = SessionManager::get_course_list_by_session_id($id_session);
	foreach ($courses as $course) {
        if ($course['code'] != $courseCode) {
            continue;
        }
        $count_users = SessionManager::get_count_users_in_course_session($course['code'], $id_session);
        $coaches = SessionManager::get_session_course_coaches_to_string($course['code'], $id_session);

		$orig_param = '&origin=resume_session';
		//hide_course_breadcrumb the parameter has been added to hide the name of the course, that appeared in the default $interbreadcrumb
		echo '
		<tr>
			<td>'.Display::url($course['title'].' ('.$course['visual_code'].')', api_get_path(WEB_COURSE_PATH).$course['code'].'/?id_session='.$id_session),'</td>
			<td>'.$coaches.'</td>
			<td>'.$count_users.'</td>
			<td>';
        echo '<a href="'.api_get_path(WEB_COURSE_PATH).$course['code'].'/index.php?id_session='.$id_session.'">'.Display::return_icon('course_home.gif', get_lang('Course')).'</a>';
        //echo '<a href="session_course_user_list.php?id_session='.$id_session.'&course_code='.$course['code'].'">'.Display::return_icon('user.png', get_lang('Edit'), '', ICON_SIZE_SMALL).'</a>';
        //echo '<a href="'.api_get_path(WEB_CODE_PATH).'/user/user_import.php?action=import&cidReq='.$course['code'].'&id_session='.$id_session.'">'.Display::return_icon('import_csv.png', get_lang('ImportUsersToACourse'), null, ICON_SIZE_SMALL).'</a>';
        echo ' <a href="../../tracking/courseLog.php?id_session='.$id_session.'&cidReq='.$course['code'].$orig_param.'&hide_course_breadcrumb=1">'.Display::return_icon('statistics.gif', get_lang('Tracking')).'</a>&nbsp;';
		//echo '<a href="session_course_edit.php?id_session='.$id_session.'&page=resume_session.php&course_code='.$course['code'].''.$orig_param.'">'.Display::return_icon('edit.png', get_lang('Edit'), '', ICON_SIZE_SMALL).'</a>';
		//echo '<a href="'.api_get_self().'?id_session='.$id_session.'&action=delete&course_code_to_delete='.$course['code'].'" onclick="javascript:if(!confirm(\''.get_lang('ConfirmYourChoice').'\')) return false;">'.Display::return_icon('delete.png', get_lang('Delete')).'</a>';
	    echo '</td>
		</tr>';
	}
}
?>
</table>
<br />

<?php

$url = Display::url(Display::return_icon('edit.png', get_lang('Edit'), array(), ICON_SIZE_SMALL), "add_users_to_session.php?page=resume_session.php&id_session=$id_session");
$url .= Display::url(Display::return_icon('import_csv.png', get_lang('ImportUsers'), array(), ICON_SIZE_SMALL), "session_user_import.php?id_session=$id_session");
$url = null;
echo Display::page_subheader(get_lang('StudentList').$url);
?>
<!--List of users -->
<table class="data_table">
    <tr>
        <th>
            <?php echo get_lang('User'); ?>
        </th>
        <th>
            <?php echo get_lang('Status'); ?>
        </th>
        <th>
            <?php echo get_lang('Information'); ?>
        </th>
        <th>
            <?php echo get_lang('Destination'); ?>
        </th>
        <th>
            <?php echo get_lang('MovedAt'); ?>
        </th>
        <th>
            <?php echo get_lang('Actions'); ?>
        </th>
    </tr>
<?php
	$orig_param = '&origin=resume_session&id_session='.$id_session; // change breadcrumb in destination page
    $users = SessionManager::get_users_by_session($id_session, 0);

    if (!empty($users)) {
        foreach ($users as $user) {
            $user_info = api_get_user_info($user['user_id']);
            //$link_class = 'class="item_disabled"';
            $link_class = null;
            $user_status_in_platform = Display::return_icon('error.png', get_lang('Inactive'));
            $information = '';
            $moved_date = '-';

            if ($user_info['active'] == 1 ) {
                $user_status_in_platform = Display::return_icon('accept.png', get_lang('Active'));
                //$link_class = null;
            } else {
                $status_info = get_latest_event_by_user_and_type($user['user_id'], LOG_USER_DEACTIVATED);
                //var_dump($status_info);
                if (!empty($status_info)) {
                    $information .= sprintf(get_lang('UserInactivedSinceX'), api_convert_and_format_date($status_info['default_date'], DATE_TIME_FORMAT_LONG));
                    $moved_date = api_get_local_time($status_info['default_date']);
                }
                $user_info['complete_name_with_username'] = Display::tag('del', $user_info['complete_name_with_username']);
            }

            $user_link = '';
            if (!empty($user['user_id'])) {
                $user_link = '<a '.$link_class.' href="'.api_get_path(WEB_CODE_PATH).'admin/course_manager/user_information.php?course='.Security::remove_XSS($courseCode).'&user_id='.intval($user['user_id']).'">
                    '.$user_info['complete_name_with_username'].
                '</a>';
            }
            $origin = null;
            $destination = null;
            $row_style = null;

            $course_link = '<a href="session_course_user.php?id_user='.$user['user_id'].'&id_session='.$id_session.'">'.Display::return_icon('course.gif', get_lang('BlockCoursesForThisUser')).'&nbsp;</a>';

            $link_to_add_user_in_url = '';

            if ($multiple_url_is_on) {
                if ($user['access_url_id'] != $url_id) {
                    $user_link .= ' '.Display::return_icon('warning.png', get_lang('UserNotAddedInURL'), array(), ICON_SIZE_SMALL);
                    $add = Display::return_icon('add.png', get_lang('AddUsersToURL'), array(), ICON_SIZE_SMALL);
                    $link_to_add_user_in_url = '<a href="resume_session.php?action=add_user_to_url&id_session='.$id_session.'&user_id='.$user['user_id'].'">'.$add.'</a>';
                }
            }

            echo '<tr '.$row_style.'>
                    <td width="30%">
                        '.$user_link.'
                    </td>
                    <td>'.$user_status_in_platform.'</td>
                    <td>'.$information.'</td>
                    <td>'.$origin.' '.$destination.'</td>
                    <td>'.$moved_date.'</td>
                    <td>
                        <a href="../../mySpace/myStudents.php?student='.$user['user_id'].''.$orig_param.'">'.Display::return_icon('statistics.gif', get_lang('Reporting')).'</a>&nbsp;
                    </td>
                    </tr>';
        }
    } else {
        echo '<tr>
			<td colspan="2">'.get_lang('NoUsersForThisSession').'</td>
		</tr>';
    }

?>
</table>
<?php
Display :: display_footer();
