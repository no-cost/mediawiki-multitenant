<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\IntValue;

class IntValueTest extends ProcessorTestBase {

	protected function getProcessor( mixed $input ): IProcessor {
		return new IntValue();
	}

	public function provideTestProcessData(): array {
		return [
			'valid-string' => [
				'input' => '3',
				'options' => [ 'min' => 0, 'max' => 9 ],
				'expected' => 3
			],
			'valid-int' => [
				'input' => 3,
				'options' => [ 'min' => 0, 'max' => 9 ],
				'expected' => 3
			],
			'over-limit' => [
				'input' => '66',
				'options' => [ 'min' => 0, 'max' => 9 ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-int-out-of-range',
					'params' => [ 'test', 66 ]
				],
				'expectException' => true
			],
			'nan' => [
				'input' => 'dummy',
				'options' => [ 'min' => 0, 'max' => 9 ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-int-not-number',
					'params' => [ 'test', 'dummy' ]
				],
				'expectException' => true
			]
		];
	}
}
