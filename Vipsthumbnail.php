<?php

namespace Floriankarsten;

use Exception;

/**
 * TBA
 *
 */
class Vipsthumbnail
{
	public $options = [];
	public $src;
	public $dst;

	public function __construct(string $src, string $dst, array $options = [])
	{
		$this->src = $src;
		$this->dst = $dst;
		// option merging: plugin defaults > config options > options from image
		$this->options = array_merge($this->defaults(), option('thumbs'), array_filter($options));

		if ($this->options['log'] === true) {
			// $this->logMessage(json_encode($this->defaults()));
			// $this->logMessage(json_encode(option('thumbs')));
			// $this->logMessage(json_encode(array_filter($options)));
			$this->logMessage(json_encode($this->options));
		}
	}

	protected function defaults(): array
	{
		return [
			'bin'           => 'vipsthumbnail',
			'interlace'     => false,
			'autoOrient'    => true,
			'crop'          => false,
			'height'        => null,
			'strip'         => false,
			'quality'       => 90,
			'width'         => null,
			'log'           => false
		];
	}

	function logMessage($log_msg)
	{
		// dumblog from stack overflow please no judging
		if (!empty($this->options['logpath'])) {
			$log_filename = $this->options['logpath'];
		} else {
			$log_filename = __DIR__ . "/logs";
		}

		if (!file_exists($log_filename)) {
			// create directory/folder uploads.
			mkdir($log_filename, 0777, true);
		}
		$log_file_data = $log_filename . '/vipthumbnaillog_' . date('d-M-Y') . '.log';
		file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
	}

	protected function autoOrient()
	{
		if ($this->options['autoOrient'] === true) {
			return '--rotate';
		}
	}

	protected function convert(): string
	{
		return sprintf($this->options['bin'] . ' %s', $this->src);
	}

	protected function interlace()
	{
		if ($this->options['interlace']) {
			return 'interlace';
		}
	}

	public function process(): string
	{

		$command = [];
		$outputOptions = [];

		$outputOptions[] = $this->strip();
		$outputOptions[] = $this->interlace();
		$outputOptions[] = $this->quality();
		$outputOptions = implode(',', array_filter($outputOptions));

		$command[] = $this->convert();
		$command[] = $this->autoOrient();
		$command[] = $this->resize();

		$command[] = $this->save($outputOptions);

		// remove all null values and join the parts
		$command = implode(' ', array_filter($command));

		if ($this->options['log'] === true) {
			$this->logMessage($command);
		}
		// try to execute the command
		exec($command, $output, $return);

		// log broken commands
		if ($return !== 0) {
			throw new Exception('The Vips convert command could not be executed: ' . $command);
		}

		return $this->dst;
	}

	protected function save($outputOptions): string
	{
		return sprintf('-o %s[%s]', $this->dst, $outputOptions);
	}


	protected function quality(): string
	{

		return 'Q=' . $this->options['quality'];
	}

	protected function resize(): string
	{
		// normalize crop
		// here it should be possible to take $this->options['crop'] "center" etc to make crops by direction
		// dirty weak check $this->options['crop'] == true if its either true or string "center" etc
		if (is_string($this->options['crop'])) {
			$this->options['crop'] = true;
		}


		// simple resize
		if ($this->options['crop'] === false) {
			return sprintf('--size %sx%s', $this->options['width'], $this->options['height']);
		}

		if ($this->options['crop'] === true && $this->options['height'] === 0) {
			// assume crop to square like ->crop(100)
			return sprintf('--size %sx%s --smartcrop attention', $this->options['width'], $this->options['width']);
		}

		if ($this->options['crop'] === true) {
			// assume crop with exact sizes
			return sprintf('--size %sx%s --smartcrop attention', $this->options['width'], $this->options['width']);
		}
	}

	protected function strip()
	{
		if ($this->options['strip']) {
			return 'strip';
		}
	}
}
