<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use PHPUnit\Framework\TestCase;
use StatusValue;

abstract class ProcessorTestBase extends TestCase {

	/**
	 * @dataProvider provideTestProcessData
	 * @param mixed $input
	 * @param array $options
	 * @param mixed $expected
	 * @param bool $expectException
	 * @return void
	 */
	public function testProcess( mixed $input, array $options, mixed $expected, bool $expectException = false ) {
		$processor = $this->getProcessor( $input );
		$processor->initializeFromSpec( $options );

		$status = $processor->process( $input, 'test' );
		$this->assertSame( !$expectException, $status->isGood(), 'Expectation to throw exception failed' );
		if ( $status->isGood() ) {
			$this->assertExpected( $expected, $status );
		} else {
			$this->assertSame( $expected, $status->getErrors()[0] );
		}
	}

	/**
	 * @param mixed $expected
	 * @param StatusValue $status
	 * @return void
	 */
	protected function assertExpected( mixed $expected, StatusValue $status ) {
		$this->assertSame( $expected, $status->getValue() );
	}

	/**
	 * @param mixed $input
	 * @return IProcessor
	 */
	abstract protected function getProcessor( mixed $input ): IProcessor;

	abstract public function provideTestProcessData(): array;
}
