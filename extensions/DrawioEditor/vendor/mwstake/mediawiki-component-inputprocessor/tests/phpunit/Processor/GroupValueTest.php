<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MediaWiki\User\UserGroupManager;
use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\UserGroupValue;

class GroupValueTest extends ProcessorTestBase {

	protected function getProcessor( mixed $input ): IProcessor {
		$ugmMock = $this->createMock( UserGroupManager::class );
		$ugmMock->method( 'listAllGroups' )->willReturn( [ 'sysop', 'Test' ] );
		$ugmMock->method( 'listAllImplicitGroups' )->willReturn( [ '*', 'user' ] );

		return new UserGroupValue( $ugmMock );
	}

	public function provideTestProcessData(): array {
		return [
			[
				'input' => 'sysop',
				'options' => [],
				'expected' => 'sysop'
			],
			[
				'input' => 'test',
				'options' => [],
				'expected' => 'Test'
			],
			[
				'input' => '*',
				'options' => [],
				'expected' => '*'
			],
			[
				'input' => 'Dummy',
				'options' => [],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-usergroup-invalid',
					'params' => [ 'test', 'Dummy' ]
				],
				'expectsException' => true,
			]
		];
	}
}
