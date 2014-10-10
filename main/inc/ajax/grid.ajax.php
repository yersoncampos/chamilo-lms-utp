<?php
/* For licensing terms, see /license.txt */
/**
 * This file contains only grids without headers or footers
 * @author Francis Gonzales <fgonzales@beeznest.com> 
 */
$language_file = array('admin', 'exercice', 'gradebook', 'tracking');

require_once '../global.inc.php';

$gridId = !empty($_REQUEST['gridId']) ? Database::escape_string($_REQUEST['gridId']) : "";
$sessionId = !empty($_REQUEST['sessionId']) ? intval($_REQUEST['sessionId']) : 0;
$courseId = !empty($_REQUEST['courseId']) ? intval($_REQUEST['courseId']) : 0;
$subgrid =  !empty($_REQUEST['subgrid']) ? Database::escape_string($_REQUEST['subgrid']) : "";

switch ($gridId) {
    case 'student_progress':
        echo MySpace::displayStudentProgressReport($sessionId, $courseId, $subgrid);
        break;
    case 'session_progress':
        echo MySpace::displaySessionProgressSummaryByCourse($courseId, $sessionId);
        break;
    default:
        echo get_lang('ParametersNotFound');
        break;
}