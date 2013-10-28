<?php

namespace modules\block_question\controllers\administrator;

use core\classes\renderable\Controller;
use core\classes\Model;
use core\classes\Pagination;
use core\classes\FormValidator;

class BlockQuestion extends Controller {

	protected $show_admin_layout = TRUE;

	protected $permissions = [
		'index' => ['administrator'],
		'config' => ['administrator'],
	];

	public function index() {
		$this->language->loadLanguageFile('administrator/block_question.php', 'modules'.DS.'block_question');
		$form_search = $this->getBlockSearchForm();

		$params = ['site_id' => ['type'=>'in', 'value'=>$this->allowedSiteIDs()]];
		if ($form_search->validate()) {
			$values = $form_search->getSubmittedValues();
			foreach ($values as $name => $value) {
				if ($name == 'search_title' && strlen($value) != 0) {
					$params['title'] = ['type'=>'like', 'value'=>'%'.$value.'%'];
				}
				elseif ($name == 'search_category' && (int)$value != 0) {
					$params['block_category_id'] = (int)$value;
				}
			}
		}

		// get all the blocks
		$pagination = new Pagination($this->request, 'title');
		$model  = new Model($this->config, $this->database);
		$block_category = $model->getModel('\core\classes\models\BlockCategory');
		$question = $model->getModel('\modules\block_question\classes\models\BlockQuestion');
		$questions = $question->getMulti($params, $pagination->getOrdering(), $pagination->getLimitOffset());
		$pagination->setRecordCount($question->getCount($params));

		$data = [
			'form' => $form_search,
			'questions' => $questions,
			'pagination' => $pagination,
			'categories' => $block_category->getAsOptions($this->allowedSiteIDs()),
		];

		$template = $this->getTemplate('pages/administrator/list.php', $data, 'modules'.DS.'block_question');
		$this->response->setContent($template->render());
	}

	public function add() {

	}

	public function edit($block_question_id) {
		$this->language->loadLanguageFile('administrator/block_question.php', 'modules'.DS.'block_question');

		$block_question_id = (int)$block_question_id;
		$model  = new Model($this->config, $this->database);
		$block = $model->getModel('\core\classes\models\Block');
		$question = $model->getModel('\modules\block_question\classes\models\BlockQuestion')->get(['id' => $block_question_id]);

		$data = [
			'is_add_page' => FALSE,
			'question' => $question->getQuestion(),
			'answer' => $question->getAnswer(),
			'solution' => $question->getSolution(),
			'theory' => $question->getTheory(),
		];

		$template = $this->getTemplate('pages/administrator/add_edit.php', $data, 'modules'.DS.'block_question');
		$this->response->setContent($template->render());
	}

	public function config() {
		$this->language->loadLanguageFile('administrator/skeleton.php', 'core'.DS.'modules'.DS.'skeleton');
		$template = $this->getTemplate('pages/administrator/skeleton.php', [], 'core'.DS.'modules'.DS.'skeleton');
		$this->response->setContent($template->render());
	}

	protected function getBlockSearchForm() {
		$inputs = [
			'search_title' => [
				'type' => 'string',
				'required' => FALSE,
				'max_length' => 256,
				'message' => $this->language->get('error_search_title'),
			],
			'search_category' => [
				'type' => 'integer',
				'required' => FALSE,
			],
		];

		return new FormValidator($this->request, 'form-block-search', $inputs);
	}
}