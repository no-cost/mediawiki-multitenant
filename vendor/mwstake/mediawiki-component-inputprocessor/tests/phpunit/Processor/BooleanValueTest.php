<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\BooleanValue;

class BooleanValueTest extends ProcessorTestBase {

	protected function getProcessor( mixed $input ): IProcessor {
		return new BooleanValue();
	}

	public function provideTestProcessData(): array {
		return [
			[
				'input' => 'true',
				'options' => [],
				'expected' => true
			],
			[
				'input' => 'yes',
				'options' => [],
				'expected' => true
			],
			[
				'input' => '1',
				'options' => [],
				'expected' => true
			],
			[
				'input' => true,
				'options' => [],
				'expected' => true
			],
			[
				'input' => 'false',
				'options' => [],
				'expected' => false
			],
			[
				'input' => 'no',
				'options' => [],
				'expected' => false
			],
			[
				'input' => '0',
				'options' => [],
				'expected' => false
			],
			[
				'input' => 0,
				'options' => [],
				'expected' => false
			],
			[
				'input' => false,
				'options' => [],
				'expected' => false
			],
			[
				'input' => 'dummy',
				'options' => [],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-boolean-not-boolean',
					'params' => [ 'test', 'dummy' ]
				],
				'expectsException' => true,
			]
		];
	}
}
