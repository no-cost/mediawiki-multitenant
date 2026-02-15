<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MediaWiki\User\UserFactory;
use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\UserValue;
use StatusValue;
use User;

class UserValueTest extends ProcessorTestBase {

	protected function getProcessor( mixed $input ): IProcessor {
		$userMock = $this->createMock( User::class );
		$userMock->method( 'getId' )->willReturn( 1 );
		$userMock->method( 'getName' )->willReturn( $input );
		$userMock->method( 'isRegistered' )->willReturn( $input === 'Dummy' );
		$userFactory = $this->createMock( UserFactory::class );
		$userFactory->method( 'newFromName' )->willReturnCallback( static function ( $name ) use ( $userMock ) {
			if ( strpos( $name, '@' ) !== false ) {
				return null;
			}
			return $userMock;
		} );

		return new UserValue( $userFactory );
	}

	/**
	 * @param mixed $expected
	 * @param StatusValue $status
	 * @return void
	 */
	protected function assertExpected( mixed $expected, StatusValue $status ) {
		$this->assertInstanceOf( User::class, $status->getValue() );
		$this->assertSame( $expected, $status->getValue()->getName() );
	}

	public function provideTestProcessData(): array {
		return [
			[
				'input' => 'Dummy',
				'options' => [],
				'expected' => 'Dummy'
			],
			[
				'input' => 'Dummy',
				'options' => [
					'mustExist' => true
				],
				'expected' => 'Dummy'
			],
			[
				'input' => 'Foo',
				'options' => [ 'mustExist' => true ],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-user-not-exist',
					'params' => [ 'test', 'Foo' ]
				],
				'expectsException' => true,
			],
			[
				'input' => '@@!!',
				'options' => [],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-user-invalid',
					'params' => [ 'test', '@@!!' ]
				],
				'expectsException' => true,
			]
		];
	}
}
