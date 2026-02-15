<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\NamespaceValue;

class NamespaceValueTest extends ProcessorTestBase {

	/**
	 * @param mixed $input
	 * @return IProcessor
	 */
	protected function getProcessor( mixed $input ): IProcessor {
		$namespaces = [
			'Main' => NS_MAIN,
			'Talk' => NS_TALK,
			'Help' => NS_HELP,
			'Help_talk' => NS_HELP_TALK
		];
		$nsInfo = $this->createMock( \NamespaceInfo::class );
		$nsInfo->method( 'getValidNamespaces' )->willReturn( array_values( $namespaces ) );
		$nsInfo->method( 'getCanonicalIndex' )->willReturnCallback( static function ( $ns ) use ( $namespaces ) {
			return $namespaces[$ns] ?? null;
		} );
		$nsInfo->method( 'isContent' )->willReturnCallback( static function ( $ns ) {
			return $ns === 0;
		} );
		$nsInfo->method( 'isSubject' )->willReturnCallback( static function ( $ns ) {
			return $ns === 0;
		} );
		$nsInfo->method( 'isTalk' )->willReturnCallback( static function ( $ns ) {
			return $ns === 1;
		} );
		return new NamespaceValue( $nsInfo, $this->createMock( \Language::class ) );
	}

	/**
	 * @return array[]
	 */
	public function provideTestProcessData(): array {
		return [
			[
				'input' => 'NS_MAIN',
				'options' => [],
				'expected' => 0
			],
			[
				'input' => '1',
				'options' => [],
				'expected' => 1
			],
			[
				'input' => 'Talk',
				'options' => [],
				'expected' => 1,
			],
			[
				'input' => NS_HELP,
				'options' => [],
				'expected' => NS_HELP,
			],
			[
				'input' => NS_MAIN,
				'options' => [ 'mustBeContent' => true ],
				'expected' => NS_MAIN,
			],
			[
				'input' => NS_HELP,
				'options' => [ 'mustBeContent' => true ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-namespace-not-content',
					'params' => [ 'test', NS_HELP ]
				],
				'expectsException' => true,
			],
			[
				'input' => NS_HELP,
				'options' => [ 'mustBeSubject' => true ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-namespace-not-subject',
					'params' => [ 'test', NS_HELP ]
				],
				'expectsException' => true,
			],
			[
				'input' => NS_HELP,
				'options' => [ 'mustBeTalk' => true ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-namespace-not-talk',
					'params' => [ 'test', NS_HELP ]
				],
				'expectsException' => true,
			],
			[
				'input' => NS_TALK,
				'options' => [ 'mustBeTalk' => true ],
				'expected' => NS_TALK
			]
		];
	}
}
