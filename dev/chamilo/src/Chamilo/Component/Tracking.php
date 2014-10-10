<?php 

namespace Chamilo\Component;

// Components
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Collection;
// Models
use Chamilo\Model\User;
use Chamilo\Model\Section;
use Chamilo\Model\Course;
use Chamilo\Model\TrackExercices;
use Chamilo\Model\QuizQuestion;

class Tracking
{
	/**
	 * get_exercise_progress
	 * @param  integer $sessionId
	 * @param  integer $courseId
	 * @param  integer  $exerciseId
	 * @param  string  $date_from
	 * @param  string  $date_to   
	 * @param  array   $options    contains order, page and limit
	 * @param  boolean $type       parameters aditional
	 * @return array
	 */
	public static function get_exercise_progress(
		$sessionId,
		$courseId,
		$exerciseId,
		$date_from = '',
		$date_to = '',
		$options = array(),
		$type = false
	) {
		$sessionId  = intval($sessionId);
		$courseId   = intval($courseId);
		$exerciseId = intval($exerciseId);

		$sessions = array();
		$courses  = array();
		// not enough data, return an empty array
		if (empty($sessionId) && empty($courseId)) { return array(); }
		// if data not empty
		if (!empty($sessionId) && empty($courseId)) {
			/**
			 * If session ID is defined but course ID is empty, 
			 * get all the courses from that session
			 */
			$session                  = Section::find($sessionId);
			$sessions[][$session->id] = $session->get()->toArray(); 
			$courses                  = $session->courses()->get()->toArray();
		} elseif (empty($sessionId) && !empty($courseId)) {
			/**
			 * If course ID defined but not sessions, get the sessions that 
			 * include this course $sessions is an array 
			 * like: [0] => ('id' => 3, 'name' => 'Session'), * [1] => (), etc;
			 */
			$course                = Course::where('id', '=', $courseId)->first();
			$courses[$course->id]  = $course;
			$sessionsTemp          = $course->sections()->get();
			$sessions[$course->id] = array_combine(
				$sessionsTemp->lists('id'), 
				$sessionsTemp->toArray()
			);
		} else if (!empty($sessionId) && !empty($courseId)) {
			// None is empty
			$course                            = Course::where('id', '=', $courseId)->first();
			$courses[$course->id]              = array($course->code);
			$courses[$course->id]['code']      = $course->code;
			$sessions[$course->id][$sessionId] = Section::find($sessionId)->toArray();
		}

		/**
		 * Now we have two arrays of courses and sessions with enough data to proceed
		 * If no course could be found, we shouldn't return anything. 
		 * Sessions can be empty (then we only return the pure-course-context results)
		 */
       	if (count($courses) < 1) { return array(); }

       	$data = array();
       	/**
       	 * The following loop is less expensive than what it seems: 
       	 * - if a course was defined, then we only loop through sessions 
       	 * - if a session was defined, then we only loop through courses 
       	 * - if a session and a course were defined, then we only loop once 
       	 */
        foreach ($courses as $courseIdx => $courseData) {
        
        	$result = TrackExercices::select(
					'track_e_exercices.session_id',
					'track_e_attempt.id as attempt_id',
					'track_e_exercices.exe_user_id as user_id',
					'track_e_exercices.exe_id as exercise_attempt_id',
					'track_e_attempt.question_id',
					'track_e_attempt.answer as answer_id',
					'track_e_attempt.tms as time',
					'track_e_exercices.exe_exo_id as quiz_id',
					new Expression(
						'CONCAT("c", c_quiz.c_id, "_e", c_quiz.id) as exercise_id'
					),
					'c_quiz.title as quiz_title',
					'c_quiz_question.description as description',
					'track_e_attempt.marks as grade',
					'track_e_exercices.exe_cours_id'
				)
        		// Join Track Attempt
				->join('track_e_attempt', function ($join) {
					$join
						->on('track_e_attempt.exe_id', '=', 'track_e_exercices.exe_id');
				})
				// Join to Quiz
				->join('c_quiz', function ($join) use ($courseIdx) {
					$join
						->on('c_quiz.id', '=', 'track_e_exercices.exe_exo_id')
						->where('c_quiz.c_id', '=', $courseIdx);
				})
				// Joint Quiz Question
				->join('c_quiz_question', function ($join) use ($courseIdx) {
					$join
						->on('c_quiz_question.id', '=', 'track_e_attempt.question_id')
						->where('c_quiz_question.c_id', '=', $courseIdx);
				})
				->where('track_e_exercices.exe_cours_id', '=', $courseData['code'])
				->whereIn('track_e_exercices.session_id', Collection::make($sessions)->collapse()->lists('id'))
				->where('c_quiz.id', 'LIKE', $exerciseId == 0 ? '%%' : $exerciseId)
				// Order
				->orderBy($options['order']['field'], $options['order']['sort'])
				// Offset ouput
				->skip(($options['page'] - 1) * $options['limit'])
				// Limit output
				->take($options['limit'])
				// Fetch data
				->get();

			$data = $result->map(function ($row, $dataIndex) use ($sessions, $courseIdx) {
				$row = $row->toArray();
				$row['session']     = $sessions[$courseIdx][$row['session_id']];
				$row['description'] = strip_tags($row['description'], '<img>');
				$row['quiz_title']  = strip_tags($row['quiz_title'], '<img>');
				$output[$dataIndex] = $row;
				return $output;
			});

			$answer   = array();
			$question = array();

			$resultQuestions = QuizQuestion::select(
					'c_quiz_question.c_id',
					'c_quiz_question.id as question_id',
					'c_quiz_question.question',
					'c_quiz_answer.id_auto',
					'c_quiz_answer.answer',
					'c_quiz_answer.correct',
					'c_quiz_question.position',
					'c_quiz_answer.id_auto as answer_id'
				)
				// Join Quiz Answer
				->join('c_quiz_answer', 'c_quiz_answer.question_id', '=', 'c_quiz_question.id')
				->where('c_quiz_question.c_id', '=', $courseIdx)
				->whereIn(
					'c_quiz_question.id', 
					array_merge(array(0), $result->lists('question_id'))
				)
				// Fecth data
				->get();

			$answer = array();
			$question = array();
			foreach ($resultQuestions->toArray() as $row) {
				$answer[$row['question_id']][$row['answer_id']] = $row;
				$question[$row['question_id']]['question'] = $row['question'];
			}

			$resultUsers = User::select(
					'user_id',
					'username',
					'firstname',
					'lastname'
				)
				->whereIn(
					'user_id',
					array_merge(array(0), $result->lists('user_id')) 
				)
				->get();
			
			$users = array_combine(
				$result->lists('user_id', 'user_id'),
				$resultUsers->toArray()
			);

			$data = $data->collapse();
			$output = new Collection;
			foreach ($data->toArray() as $id => $row) {
				$rowQuestId = $row['question_id'];
                $rowAnsId = $row['answer_id'];
                $output->put($id, [
            		'session_id'          => $row['session_id'],
		            'attempt_id'          => $row['attempt_id'],
		            'user_id'             => $row['user_id'],
		            'exercise_attempt_id' => $row['exercise_attempt_id'],
		            'question_id'         => $row['question_id'],
		            'answer_id'           => $row['answer_id'],
		            'time'                => $row['time'],
		            'quiz_id'             => $row['quiz_id'],
		            'exercise_id'         => $row['exercise_id'],
		            'quiz_title'          => $row['quiz_title'],
		            'description'         => $row['description'],
		            'grade'               => $row['grade'],
		            'exe_cours_id'        => $row['exe_cours_id'],
            		'session'             => $sessions[$courseIdx][$row['session_id']]['name'],
            		'firstname'           => $users[$row['user_id']]['firstname'],
            		'lastname'            => $users[$row['user_id']]['lastname'],
            		'username'            => $users[$row['user_id']]['username'],
            		'answer'              => $answer[$rowQuestId][$rowAnsId]['answer'],
            		'correct'             => ($answer[$rowQuestId][$rowAnsId]['correct'] == 0 ? 'No' : 'Yes'),
            		'question'            => $question[$rowQuestId]['question'],
            		'question_id'         => $rowQuestId,
            		'course'              => $row['exe_cours_id']
            	]);
			}
        }

       	return $output->toArray();
	}

}

/* End of file Tracking.php */
/* Location: dev/chamilo/src/Chamilo/Component/Tracking.php */