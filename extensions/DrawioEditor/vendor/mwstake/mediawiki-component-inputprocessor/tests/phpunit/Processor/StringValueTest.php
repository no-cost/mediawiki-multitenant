<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\StringValue;

class StringValueTest extends ProcessorTestBase {

	protected function getProcessor( mixed $input ): IProcessor {
		return new StringValue();
	}

	public function provideTestProcessData(): array {
		return [
			[
				'input' => 'test',
				'options' => [],
				'expected' => 'test'
			],
			[
				'input' => 123,
				'options' => [],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-string-not-string',
					'params' => [ 'test', 123 ]
				],
				'expectsException' => true,
			]
		];
	}
}
