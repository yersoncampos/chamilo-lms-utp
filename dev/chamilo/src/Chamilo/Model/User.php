<?php 

namespace Chamilo\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
	protected $table = 'user';

	public function courses()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\Course',
			// Table Pivot
			'course_rel_user',
			// User FK in table pivot
			'user_id',
			// Course FK in table pivot
			'course_code'
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

	public function friends()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\User',
			// Table Pivot
			'user_rel_user',
			// User FK in table pivot
			'user_id',
			// Chamilo\Model\User FK in table pivot
			'friend_user_id'
		)
			->withPivot('relation_type', 'last_edit');
	}

	public function tags()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\Tag',
			// Table Pivot
			'user_rel_tag',
			// User FK in table pivot
			'user_id',
			// Tag FK in table pivot
			'tag_id'
		);
	}

	public function sections()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\Section',
			// Table Pivot
			'session_rel_user',
			// User FK in table pivot
			'id_user',
			// Section FK in table Pivot
			'id_session'
		)
			->withPivot('relation_type');
	}

	public function classes()
	{
		return $this->belongsToMany(
			// Table Related
			'Chamilo\Model\Class',
			// Table Pivot
			'class_user',
			// User FK in table pivot
			'user_id',
			// Class FK in table pivot
			'class_id'
		);
	}

	public function fieldValues()
	{
		return $this->hasMany(
			// Table Related
			'Chamilo\Model\UserFieldValues',
			// User FK in table related
			'user_id',
			// User PK
			'user_id'
		);
	}

}

/* End of file User.php */
/* Location: dev/chamilo/src/Chamilo/Model/User.php */