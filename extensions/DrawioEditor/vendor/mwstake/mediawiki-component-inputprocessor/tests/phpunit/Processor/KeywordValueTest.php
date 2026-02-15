<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\KeywordValue;

class KeywordValueTest extends ProcessorTestBase {

	protected function getProcessor( mixed $input ): IProcessor {
		return new KeywordValue();
	}

	public function provideTestProcessData(): array {
		return [
			[
				'input' => 'foo',
				'options' => [ 'keywords' => [ 'foo', 'bar' ] ],
				'expected' => 'foo'
			],
			[
				'input' => 'FoO',
				'options' => [ 'keywords' => [ 'Foo', 'bar' ] ],
				'expected' => 'Foo'
			],
			[
				'input' => 'dummy',
				'options' => [ 'keywords' => [ 'foo', 'bar' ] ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-keyword-not-keyword',
					'params' => [ 'test', 'dummy' ]
				],
				'expectsException' => true,
			]
		];
	}
}
