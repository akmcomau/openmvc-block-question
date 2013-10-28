<?php

namespace modules\block_question\classes\models;

use core\classes\Model;
use core\classes\models\BlockCategory;

class BlockQuestion extends Model {

	protected $table       = 'block_question';
	protected $primary_key = 'block_question_id';
	protected $columns     = [
		'block_question_id' => [
			'data_type'      => 'int',
			'auto_increment' => TRUE,
			'null_allowed'   => FALSE,
		],
		'block_question_title' => [
			'data_type'      => 'text',
			'data_length'    => 64,
			'null_allowed'   => FALSE,
		],
		'theory_block_id' => [
			'data_type'      => 'int',
			'null_allowed'   => TRUE,
		],
		'question_block_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'answer_block_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'solution_block_id' => [
			'data_type'      => 'int',
			'null_allowed'   => TRUE,
		],
	];

	protected $foreign_keys = [
		'theory_block_id'   => ['block', 'block_id'],
		'question_block_id' => ['block', 'block_id'],
		'answer_block_id'   => ['block', 'block_id'],
		'solution_block_id' => ['block', 'block_id'],
	];

	protected $relationships = [
		'block_category' => [
			'where_fields'  => ['block_category_id'],
			'join_clause'   => 'LEFT JOIN block_question_category_link USING (block_question_id) LEFT JOIN block_category USING (block_category_id)',
		],
	];

	public function setCategory(BlockCategory $category = NULL) {
		$this->objects['category'] = $category;
	}

	public function getCategoryName() {
		$category = $this->getCategory();
		return $category ? $category->name : NULL;
	}

	public function getCategory() {
		// object is not in the database
		if (!$this->id) {
			return NULL;
		}

		if (!isset($this->objects['category'])) {
			$sql = "
				SELECT block_category.*
				FROM
					block_question_category_link
					JOIN block_category USING (block_category_id)
				WHERE
					block_question_id=".$this->database->quote($this->id)."
			";
			$record = $this->database->querySingle($sql);
			if ($record) {
				 $this->objects['category'] = $this->getModel('\\core\\classes\\models\\BlockCategory', $record);
			}
			else {
				$this->objects['category'] =  NULL;
			}
		}
		return $this->objects['category'];
	}

	public function setTheory(Block $block = NULL) {
		$this->objects['theory'] = $block;
	}

	public function getTheory() {
		if (isset($this->objects['theory'])) {
			return $this->objects['theory'];
		}

		if ($this->theory_block_id) {
			return $this->getModel('\\core\\classes\\models\\Block')->get(['id' => $this->theory_block_id]);
		}

		return NULL;
	}

	public function setQuestion(Block $block = NULL) {
		$this->objects['question'] = $block;
	}

	public function getQuestion() {
		if (isset($this->objects['question'])) {
			return $this->objects['question'];
		}

		if ($this->question_block_id) {
			return $this->getModel('\\core\\classes\\models\\Block')->get(['id' => $this->question_block_id]);
		}

		return NULL;
	}

	public function setAnswer(Block $block = NULL) {
		$this->objects['answer'] = $block;
	}

	public function getAnswer() {
		if (isset($this->objects['answer'])) {
			return $this->objects['answer'];
		}

		if ($this->answer_block_id) {
			return $this->getModel('\\core\\classes\\models\\Block')->get(['id' => $this->answer_block_id]);
		}

		return NULL;
	}

	public function setSolution(Block $block = NULL) {
		$this->objects['solution'] = $block;
	}

	public function getSolution() {
		if (isset($this->objects['solution'])) {
			return $this->objects['solution'];
		}

		if ($this->solution_block_id) {
			return $this->getModel('\\core\\classes\\models\\Block')->get(['id' => $this->solution_block_id]);
		}

		return NULL;
	}

	public function insert() {
		// update the block
		parent::insert();

		if (isset($this->objects['category']) && $this->objects['category']) {
			// insert the category
			$link = $this->getModel('\modules\block_question\classes\models\BlockQuestionCategoryLink');
			$link->block_question_id = $this->id;
			$link->block_category_id = $this->objects['category']->id;
			$link->insert();
		}
	}

	public function update() {
		// update the block
		parent::update();

		// get the link
		$link = $this->getModel('\modules\block_question\classes\models\BlockQuestionCategoryLink')->get([
			'block_question_id' => $this->id,
		]);

		// update the category
		$category = $this->objects['category'];
		if ($category && $link) {
			// update the category
			$link->block_category_id = $category->id;
			$link->update();
		}
		elseif ($category && !$link) {
			// insert the category
			$link = $this->getModel('\modules\block_question\classes\models\BlockQuestionCategoryLink');
			$link->block_question_id = $this->id;
			$link->block_category_id = $category->id;
			$link->insert();
		}
		elseif (!$category && $link) {
			// remove the link
			$link->delete();
		}
	}

	public function delete() {
		// delete all block_question_category_link
		$sql = "DELETE FROM block_question_category_link WHERE block_question_id=".$this->database->quote($this->id);
		$this->database->executeQuery($sql);

		parent::delete();
	}
}
