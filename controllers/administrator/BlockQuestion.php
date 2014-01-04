<?php

namespace modules\block_question\controllers\administrator;

use core\classes\exceptions\RedirectException;
use core\classes\renderable\Controller;
use core\classes\Model;
use core\classes\Pagination;
use core\classes\FormValidator;
use modules\block_question\classes\models\BlockQuestion as BlockQuestionModel;

class BlockQuestion extends Controller {

	protected $show_admin_layout = TRUE;

	protected $permissions = [
		'index' => ['administrator'],
		'config' => ['administrator'],
	];

	public function index() {
		$this->language->loadLanguageFile('administrator/block_question.php', 'modules'.DS.'block_question');
		$form_search = $this->getQuestionSearchForm();

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
		$module_config = $this->config->moduleConfig('Block Question');
		$this->language->loadLanguageFile('administrator/block_question.php', 'modules'.DS.'block_question');

		$model  = new Model($this->config, $this->database);
		$block = $model->getModel('\core\classes\models\Block');
		$block_category = $model->getModel('\core\classes\models\BlockCategory');
		$question = $model->getModel('\modules\block_question\classes\models\BlockQuestion');
		$question->site_id = $this->config->siteConfig()->site_id;

		$types = [];
		foreach ($module_config->types as $type) {
			$types[$type->id] = $type->name;
		}

		$form = $this->getQuestionForm();
		if ($form->validate()) {
			$this->updateFromRequest($form, $question);
			$question->insert();
			$form->setNotification('success', $this->language->get('notification_add_success'));
			throw new RedirectException($this->url->getUrl('administrator/BlockQuestion', 'index', ['add-success']));
		}
		elseif ($form->isSubmitted()) {
			$this->updateFromRequest($form, $question);
			$form->setNotification('error', $this->language->get('notification_add_error'));
		}

		$data = [
			'is_add_page' => FALSE,
			'question' => $question,
			'form' => $form,
			'categories' => $block_category->getAsOptions($this->allowedSiteIDs()),
			'types' => $types,
		];

		$template = $this->getTemplate('pages/administrator/add_edit.php', $data, 'modules'.DS.'block_question');
		$this->response->setContent($template->render());
	}

	public function edit($block_question_id) {
		$module_config = $this->config->moduleConfig('Block Question');
		$this->language->loadLanguageFile('administrator/block_question.php', 'modules'.DS.'block_question');

		$block_question_id = (int)$block_question_id;
		$model  = new Model($this->config, $this->database);
		$block = $model->getModel('\core\classes\models\Block');
		$block_category = $model->getModel('\core\classes\models\BlockCategory');
		$question = $model->getModel('\modules\block_question\classes\models\BlockQuestion')->get(['id' => $block_question_id]);
		$this->siteProtection($question);

		$types = [];
		foreach ($module_config->types as $type) {
			$types[$type->id] = $type->name;
		}

		$form = $this->getQuestionForm();
		if ($form->validate()) {
			$this->updateFromRequest($form, $question);
			$question->update();
			$form->setNotification('success', $this->language->get('notification_update_success'));
			throw new RedirectException($this->url->getUrl('administrator/BlockQuestion', 'index', ['update-success']));
		}
		elseif ($form->isSubmitted()) {
			$this->updateFromRequest($form, $question);
			$form->setNotification('error', $this->language->get('notification_update_error'));
		}

		$data = [
			'is_add_page' => FALSE,
			'question' => $question,
			'form' => $form,
			'categories' => $block_category->getAsOptions($this->allowedSiteIDs()),
			'types' => $types,
		];

		$template = $this->getTemplate('pages/administrator/add_edit.php', $data, 'modules'.DS.'block_question');
		$this->response->setContent($template->render());
	}

	protected function updateFromRequest(FormValidator $form, BlockQuestionModel $question) {
		$question->title = $form->getValue('title');
		$question->title = $form->getValue('title');
		$question->number = $form->getValue('number');
		$question->sub_number = $form->getValue('sub_number');
		$question->type = $form->getValue('type');

		if ($question->sub_number == '') {
			$question->sub_number = NULL;
		}

		$block = $question->getModel('\core\classes\models\Block');
		$theory = $block->get(['tag' => $form->getValue('theory')]);
		if ($theory) {
			$question->theory_block_id = $theory->id;
		}
		else {
			$question->theory_block_id = NULL;
		}

		$question_block = $block->get(['tag' => $form->getValue('question')]);
		if ($question_block) {
			$question->question_block_id = $question_block->id;
		}
		else {
			$question->question_block_id = NULL;
		}

		$answer = $block->get(['tag' => $form->getValue('answer')]);
		if ($answer) {
			$question->answer_block_id = $answer->id;
		}
		else {
			$question->answer_block_id = NULL;
		}

		$solution = $block->get(['tag' => $form->getValue('solution')]);
		if ($solution) {
			$question->solution_block_id = $solution->id;
		}
		else {
			$question->solution_block_id = NULL;
		}

		$block->setCategory(NULL);
		if ((int)$form->getValue('category')) {
			$block_category = $block->getModel('\core\classes\models\BlockCategory')->get([
				'id' => (int)$form->getValue('category'),
			]);
			if ($block_category) {
				$question->setCategory($block_category);
			}
		}
	}

	public function config() {
		$this->language->loadLanguageFile('administrator/skeleton.php', 'core'.DS.'modules'.DS.'skeleton');
		$template = $this->getTemplate('pages/administrator/skeleton.php', [], 'core'.DS.'modules'.DS.'skeleton');
		$this->response->setContent($template->render());
	}

	protected function getQuestionSearchForm() {
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

	protected function getQuestionForm() {
		$inputs = [
			'title' => [
				'type' => 'string',
				'required' => TRUE,
				'max_length' => 256,
				'message' => $this->language->get('error_title'),
			],
			'number' => [
				'type' => 'integer',
				'required' => TRUE,
				'message' => $this->language->get('error_number'),
			],
			'sub_number' => [
				'type' => 'integer',
				'required' => FALSE,
				'message' => $this->language->get('error_sub_number'),
			],
			'category' => [
				'type' => 'integer',
				'required' => FALSE,
				'message' => $this->language->get('error_category'),
			],
			'type' => [
				'type' => 'integer',
				'required' => TRUE,
				'message' => $this->language->get('error_type'),
			],
			'theory' => [
				'type' => 'string',
				'required' => FALSE,
				'message' => $this->language->get('error_theory'),
			],
			'question' => [
				'type' => 'string',
				'required' => TRUE,
				'message' => $this->language->get('error_question'),
			],
			'answer' => [
				'type' => 'string',
				'required' => TRUE,
				'message' => $this->language->get('error_answer'),
			],
			'solution' => [
				'type' => 'string',
				'required' => FALSE,
				'message' => $this->language->get('error_solution'),
			],
		];

		$model  = new Model($this->config, $this->database);
		$block = $model->getModel('\core\classes\models\Block');
		$check_block_tag = function($value, $form) use ($model, $block) {
			if ($value == '') {
				return TRUE;
			}
			$exists = $block->get(['tag' => $value]);
			if ($exists) {
				return TRUE;
			}
			else {
				$block_category = NULL;
				if ((int)$form->getValue('category')) {
					$block_category = $model->getModel('\core\classes\models\BlockCategory')->get([
						'id' => (int)$form->getValue('category'),
					]);
				}

				$type = $model->getModel('\core\classes\models\BlockType')->get(['name' => $this->config->siteConfig()->default_block_type]);

				$block = $model->getModel('\core\classes\models\Block');
				$block->site_id = $this->config->siteConfig()->site_id;
				$block->type_id = $type->id;
				$block->tag = $value;
				$block->title = $value;
				$block->content = '';
				$block->setCategory($block_category);
				$block->insert();
				return TRUE;
			}
		};

		$validators = [
			'theory' => [
				[
					'type'     => 'function',
					'message'  => $this->language->get('error_block_not_found'),
					'function' => $check_block_tag,
				],
			],
			'question' => [
				[
					'type'     => 'function',
					'message'  => $this->language->get('error_block_not_found'),
					'function' => $check_block_tag,
				],
			],
			'answer' => [
				[
					'type'     => 'function',
					'message'  => $this->language->get('error_block_not_found'),
					'function' => $check_block_tag,
				],
			],
			'solution' => [
				[
					'type'     => 'function',
					'message'  => $this->language->get('error_block_not_found'),
					'function' => $check_block_tag,
				],
			],
		];

		return new FormValidator($this->request, 'form-question', $inputs, $validators);
	}
}