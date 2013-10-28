<?php

namespace modules\block_question;

use ErrorException;
use core\classes\Config;
use core\classes\Database;
use core\classes\Language;
use core\classes\Model;
use core\classes\Menu;

class Installer {
	protected $config;
	protected $database;

	public function __construct(Config $config, Database $database) {
		$this->config = $config;
		$this->database = $database;
	}

	public function install() {
		$model = new Model($this->config, $this->database);

		// create block_mathjax database table
		$table = $model->getModel('\\modules\\block_question\\classes\\models\\BlockQuestion');
		$table->createTable();
		$table = $model->getModel('\\modules\\block_question\\classes\\models\\BlockQuestionCategoryLink');
		$table->createTable();
	}

	public function uninstall() {
		$model = new Model($this->config, $this->database);


		// drop block_mathjax database table
		$table = $model->getModel('\\modules\\block_question\\classes\\models\\BlockQuestionCategoryLink');
		$table->dropTable();
		$table = $model->getModel('\\modules\\block_question\\classes\\models\\BlockQuestion');
		$table->dropTable();
	}

	public function enable() {
		$language = new Language($this->config);
		$language->loadLanguageFile('administrator/block_question.php', DS.'modules'.DS.'block_question');

		$layout_strings = $language->getFile('administrator/layout.php');
		$layout_strings['block_questions_module'] = $language->get('block_questions');
		$language->updateFile('administrator/layout.php', $layout_strings);

		$main_menu = new Menu($this->config, $language);
		$main_menu->loadMenu('menu_admin_main.php');
		$main_menu->insert_menu(['content', 'content_blocks'], 'content_questions', [
			'controller' => 'administrator/BlockQuestion',
			'method' => 'index',
			'text_tag' => 'block_questions_module',
			'children' => [
				'content_questions_list' => [
					'controller' => 'administrator/BlockQuestion',
					'method' => 'index',
				],
				'content_questions_add' => [
					'controller' => 'administrator/BlockQuestion',
					'method' => 'add',
				],
			],
		]);


		$main_menu->update();
	}

	public function disable() {
		$language = new Language($this->config);
		$language->loadLanguageFile('administrator/block_question.php', DS.'modules'.DS.'block_question');

		$layout_strings = $language->getFile('administrator/layout.php');
		unset($layout_strings['block_questions_module']);
		$language->updateFile('administrator/layout.php', $layout_strings);

		// Remove some menu items to the admin menu
		$main_menu = new Menu($this->config, $language);
		$main_menu->loadMenu('menu_admin_main.php');
		$menu = $main_menu->getMenuData();

		unset($menu['content']['children']['content_questions']);

		$main_menu->setMenuData($menu);
		$main_menu->update();
	}
}