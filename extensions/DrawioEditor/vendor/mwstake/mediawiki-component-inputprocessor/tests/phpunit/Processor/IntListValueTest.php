<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\IntListValue;

class IntListValueTest extends ProcessorTestBase {

	protected function getProcessor( mixed $input ): IProcessor {
		return new IntListValue();
	}

	public function provideTestProcessData(): array {
		return [
			'valid' => [
				'input' => '3|5;8,10',
				'options' => [ 'min' => 0, 'max' => 10 ],
				'expected' => [ 3, 5, 8, 10 ]
			],
			'invalid-separator' => [
				'input' => '3|5;8,10',
				'options' => [ 'separator' => ',' ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-int-not-number',
					'params' => [ 'test', '3|5;8' ]
				],
				'expectException' => true
			],
			'over-limit' => [
				'input' => '1|2|3|66',
				'options' => [ 'min' => 0, 'max' => 9 ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-int-out-of-range',
					'params' => [ 'test', 66 ]
				],
				'expectException' => true
			]
		];
	}
}
