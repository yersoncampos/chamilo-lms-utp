<?php

namespace Chamilo\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Course extends Eloquent
{
	protected $table = 'course';
	protected $primaryKey = 'code';

	public function getById($id)
	{
		return $this::where('id', '=', $id);
	}

	public function getByCode($code)
	{
		return $this::find($code);
	}

	public function sections()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\Section', 
			// Table Pivot
			'session_rel_course', 
			// Course FK in table pivot
			'course_code', 
			// Section FK in table pivot
			'id_session'
		)
			->withPivot('nbr_users');
	}

	public function users()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\User',
			// Table Pivot
			'course_rel_user',
			// Course FK in table pivot
			'course_code',
			// User FK in table pivot
			'user_id'
		)
			->withPivot(
				'status', 
				'role', 
				'group_id', 
				'tutor_id', 
				'sort', 
				'user_course_cat', 
				'relation_type', 
				'legal_agreement'
			);
	}
}

/* End of file Course.php */
/* Location: dev/chamilo/src/Chamilo/Model/Course.php */