<?php
App::uses('TwitterBootstrapAppShell', 'TwitterBootstrap.Console/Command');

class CopyShell extends TwitterBootstrapAppShell {

	public function getOptionParser() {
		$options = array(
			'theme' => array(
				'short' => 't',
				'help' => __('Set theme to place Bootstrap files in.'),
				'boolean' => false
			),
			'webroot' => array(
				'short' => 'w',
				'help' => __('Set file output to webroot Theme dir (use with theme option).'),
				'boolean' => true
			)
		);

		return ConsoleOptionParser::buildFromArray(array(
			'command' => 'copy',
			'description' => __('TwitterBootstrap Copy Shell Help.'),
			'options' => array(
				'theme' => $options['theme'],
				'webroot' => $options['webroot']
			),
			'subcommands' => array(
				'all' => array(
					'help' => __('Copies Less, Js & Assets source from Bootstrap submodule in plugin Vendor dir'),
					'parser' => array(
						'description' => array(__('files will be placed in webroot of App or named Theme')),
						'options' => array(
							'theme' => $options['theme'],
							'webroot' => $options['webroot']
						),
					)
				),
				'less' => array(
					'help' => __('Copies Less source from Bootstrap submodule in plugin Vendor dir'),
					'parser' => array(
						'description' => array(__('files will be placed in webroot/css/lib/ of App or named Theme')),
						'options' => array(
							'theme' => $options['theme'],
							'webroot' => $options['webroot']
						),
					)
				),
				'assets' => array(
					'help' => __('Copies Assets source from Bootstrap submodule in plugin Vendor dir'),
					'parser' => array(
						'description' => array(__('files will be placed in webroot/assets/ of App or named Theme')),
						'options' => array(
							'theme' => $options['theme'],
							'webroot' => $options['webroot']
						),
					)
				),
				'js' => array(
					'help' => __('Copies Js source from Bootstrap submodule in plugin Vendor dir'),
					'parser' => array(
						'description' => array(__('files will be placed in webroot/js/lib of App or named Theme')),
						'options' => array(
							'theme' => $options['theme'],
							'webroot' => $options['webroot']
						),
					)
				)
			)
		));
	}

	public function main() {
		if (isset($this->params['theme'])) {
			$this->_Theme = $this->params['theme'];
		}

		$this->_Action = isset($this->args[0]) ? $this->args[0] : 'all';
		switch ($this->_Action) {
			case 'js':
				$this->copyJs();
				break;
			case 'less':
				$this->copyLess();
				break;
			case 'assets':
				$this->copyAssets();
				break;
			default:
				$this->copyLess();
				$this->copyAssets();
				$this->copyJs();
				break;
		}
	}

	protected function _copy($options) {
		$default = array(
			'from' => null,
			'to' => null,
			'skip' => array('tests', 'README.md'),
		);
		$options += $default;

		$this->out('<comment>From: ' . str_replace(APP, '', $options['from']) . "\n" .
			 'To: ' . str_replace(APP, '', $options['to']) . '</comment>', 1, Shell::VERBOSE);

		if ($this->Folder->copy($options)) {
			$this->out('<info>' . __('Success.', 'TwitterBootstrap') . '</info>');
		} else {
			$this->out('<error>' . __('Error!', 'TwitterBootstrap') . '</error>');
		}
	}

	public function copyLess() {
		$from = $this->bootstrapPath . self::LESS_DIR;
		$to = '';
		if ($this->_Theme && !isset($this->params['webroot'])) {
			$to = APP . 'View' . DS . 'Themed' . DS . $this->_Theme . DS . 'webroot' . DS . 'css' . DS . 'lib';
		} elseif ($this->_Theme && isset($this->params['webroot'])) {
			$to = WWW_ROOT . 'theme' . DS . $this->_Theme . DS . 'css' . DS . 'lib';
		} else {
			$to = WWW_ROOT . 'css' . DS . 'less';
		}
		$this->out('<info>Copying Less</info>');
		$this->_copy(compact('from', 'to'));
	}

	public function copyAssets() {
		$from = $this->bootstrapPath . self::ASSETS_DIR;
		$to = '';
		if ($this->_Theme && !isset($this->params['webroot'])) {
			$to = APP . 'View' . DS . 'Themed' . DS . $this->_Theme . DS . 'webroot' . DS . 'assets';
		} elseif ($this->_Theme && isset($this->params['webroot'])) {
			$to = WWW_ROOT . 'theme' . DS . $this->_Theme . DS . 'assets';
		} else {
			$to = WWW_ROOT . 'assets';
		}
		$this->out('<info>Copying Assets</info>');
		$this->_copy(compact('from', 'to'));
	}

	public function copyJs() {
		$from = $this->bootstrapPath . self::JS_DIR;
		$to = '';
		if ($this->_Theme && !isset($this->params['webroot'])) {
			$to = APP . 'View' . DS . 'Themed' . DS . $this->_Theme . DS . 'webroot' . DS . 'js' . DS . 'lib';
		} elseif ($this->_Theme && isset($this->params['webroot'])) {
			$to = WWW_ROOT . 'theme' . DS . $this->_Theme . DS . 'js' . DS . 'lib';
		} else {
			$to = WWW_ROOT . 'js' . DS . 'lib';
		}
		$this->out('<info>Copying Javascript</info>');
		$this->_copy(compact('from', 'to'));
	}

}
