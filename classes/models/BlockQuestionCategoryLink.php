<?php

namespace modules\block_question\classes\models;

use core\classes\Model;

class BlockQuestionCategoryLink extends Model {

	protected $table       = 'block_question_category_link';
	protected $primary_key = 'block_question_category_link_id';
	protected $columns     = [
		'block_question_category_link_id' => [
			'data_type'      => 'int',
			'auto_increment' => TRUE,
			'null_allowed'   => FALSE,
		],
		'block_question_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'block_category_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
	];

	protected $indexes = [
		'block_question_id',
		'block_category_id',
	];

	protected $foreign_keys = [
		'block_question_id' => ['block_question', 'block_question_id'],
		'block_category_id' => ['block_category', 'block_category_id'],
	];
}
