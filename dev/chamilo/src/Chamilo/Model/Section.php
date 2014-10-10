<?php

namespace Chamilo\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Section extends Eloquent
{
	protected $table = 'session';

	public function getById($id)
	{
		return $this->find($id);
	}

	public function courses()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\Course', 
			// Table Pivot
			'session_rel_course',
			// Section FK in table pivot
			'id_session',
			// Course FK in table pivot
			'course_code'
		)
			->withPivot('nbr_users');
	}

	public function users()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\User',
			// Table Pivot
			'session_rel_user',
			// Section FK in table pivot
			'id_session',
			// User FK in table pivot
			'id_user'
		)
			->withPivot('relation_type');
	}
}

/* End of file Section.php */
/* Location: dev/chamilo/src/Chamilo/Model/Section.php */