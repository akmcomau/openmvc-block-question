<?php

namespace modules\block_question\controllers;

use core\classes\renderable\Controller;
use core\classes\Model;
use core\classes\Pagination;
use core\classes\FormValidator;

class BlockQuestion extends Controller {

	public function random() {
		$this->language->loadLanguageFile('administrator/block_question.php', 'modules'.DS.'block_question');

		$model  = new Model($this->config, $this->database);
		$block = $model->getModel('\core\classes\models\Block');
		$question = $model->getModel('\modules\block_question\classes\models\BlockQuestion')->get(['get_random_record' => TRUE]);

		if (!$question) {
			$template = $this->getTemplate('pages/no_questions.php', [], 'modules'.DS.'block_question');
			$this->response->setContent($template->render());
			return;
		}

		$data = [
			'title' => $question->title,
			'question' => $question->getQuestion(),
			'answer' => $question->getAnswer(),
			'solution' => $question->getSolution(),
			'theory' => $question->getTheory(),
		];

		$template = $this->getTemplate('pages/random.php', $data, 'modules'.DS.'block_question');
		$this->response->setContent($template->render());
	}

	public function browse() {

	}

	public function search() {

	}
}