<?php
/* For licensing terms, see /license.txt */
/**
 * Index page of the admin tools
 * @package chamilo.admin
 */
/**
 * Code
 */
// Language files that need to be included.
$language_file = array('admin', 'tracking','coursebackup');

// Resetting the course id.
$cidReset = true;

// Including some necessary chamilo files.
require_once '../../inc/global.inc.php';

// Setting the section (for the tabs).
$this_section = SECTION_PLATFORM_ADMIN;

// Access restrictions.
if (!api_is_course_manager_admin()) {
    api_not_allowed();
}

$nameTools = get_lang('PlatformAdmin');

// Displaying the header
$message = '';


if (isset($_GET['msg']) && isset($_GET['type'])) {
    if (in_array($_GET['msg'], array('ArchiveDirCleanupSucceeded', 'ArchiveDirCleanupFailed')))
        switch($_GET['type']) {
            case 'error':
                $message = Display::return_message(get_lang($_GET['msg']), 'error');
                break;
            case 'confirmation':
                $message = Display::return_message(get_lang($_GET['msg']), 'confirm');
        }
}

$blocks = array();
/* Courses */
$blocks['courses']['icon']  = Display::return_icon('course.gif', get_lang('Courses'), array(), ICON_SIZE_MEDIUM, false);
$blocks['courses']['label'] = api_ucfirst(get_lang('Courses'));

$search_form = ' <form method="get" class="form-search" action="course_list.php">
                        <input class="span3" type="text" name="keyword" value="">
                        <button class="btn" type="submit">'.get_lang('Search').'</button>
                    </form>';
$blocks['courses']['search_form'] = $search_form;

$items = array();
$items[] = array('url'=>'course_list.php', 	'label' => get_lang('CourseList'));
//$items[] = array('url'=>'course_add.php', 	'label' => get_lang('AddCourse'));
/*$items[] = array('url'=>'course_export.php', 			'label' => get_lang('ExportCourses'));
$items[] = array('url'=>'course_import.php', 			'label' => get_lang('ImportCourses'));
$items[] = array('url'=>'course_category.php', 			'label' => get_lang('AdminCategories'));
$items[] = array('url'=>'subscribe_user2course.php', 	'label' => get_lang('AddUsersToACourse'));
$items[] = array('url'=>'course_user_import.php', 		'label' => get_lang('ImportUsersToACourse'));*/

$blocks['courses']['items'] = $items;
$blocks['courses']['extra'] = null;

$tpl = new Template();
$tpl->assign('web_admin_ajax_url', $admin_ajax_url);
$tpl->assign('blocks', $blocks);
// The template contains the call to the AJAX version checker
$admin_template = $tpl->get_template('admin/settings_index.tpl');
$content = $tpl->fetch($admin_template);
$tpl->assign('content', $content);
$tpl->assign('message', $message);
$tpl->display_one_col_template();
