<?php

namespace MWStake\MediaWiki\Component\InputProcessor\Tests\Processor;

use MediaWiki\Permissions\PermissionManager;
use MWStake\MediaWiki\Component\InputProcessor\IProcessor;
use MWStake\MediaWiki\Component\InputProcessor\Processor\TitleValue;
use StatusValue;
use Title;
use TitleFactory;
use User;

class TitleValueTest extends ProcessorTestBase {

	/**
	 * @param mixed $input
	 * @return IProcessor
	 */
	protected function getProcessor( mixed $input ): IProcessor {
		$titleMock = $this->createMock( Title::class );
		$titleMock->method( 'getNamespace' )->willReturn( 0 );
		$titleBits = explode( ':', $input );
		$titleMock->method( 'getDBkey' )->willReturn( array_pop( $titleBits ) );
		$titleMock->method( 'getPrefixedDBkey' )->willReturn( $input );
		$titleMock->method( 'exists' )->willReturn( true );
		$titleFactoryMock = $this->createMock( TitleFactory::class );
		$titleFactoryMock->method( 'newFromText' )->willReturn( $titleMock );
		$permissionManagerMock = $this->createMock( PermissionManager::class );
		$permissionManagerMock->method( 'userCan' )->willReturnCallback(
			static function ( $action, $user, $title ) {
				return $user->getName() === 'Dummy';
			}
		);
		return $this->doGetProcessor( $titleFactoryMock, $permissionManagerMock );
	}

	/**
	 * @param TitleFactory $titleFactory
	 * @param PermissionManager $permissionManager
	 * @return TitleValue
	 */
	protected function doGetProcessor( TitleFactory $titleFactory, PermissionManager $permissionManager ) {
		return new TitleValue( $titleFactory, $permissionManager );
	}

	/**
	 * @param mixed $expected
	 * @param StatusValue $status
	 * @return void
	 */
	protected function assertExpected( mixed $expected, StatusValue $status ) {
		$this->assertInstanceOf( Title::class, $status->getValue() );
		$this->assertSame( $expected, $status->getValue()->getDBkey() );
	}

	/**
	 * @return array[]
	 */
	public function provideTestProcessData(): array {
		$allowedUser = $this->createMock( User::class );
		$allowedUser->method( 'getName' )->willReturn( 'Dummy' );
		$disallowedUser = $this->createMock( User::class );
		$disallowedUser->method( 'getName' )->willReturn( 'Dummy2' );

		return [
			[
				'input' => 'Test',
				'options' => [
					'userMustBeAbleToRead' => $allowedUser,
					'allowedNamespaces' => [ 0 ]
				],
				'expected' => 'Test'
			],
			[
				'input' => 'Test',
				'options' => [
					'userMustBeAbleToRead' => $disallowedUser,
					'allowedNamespaces' => [ 0 ]
				],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-title-no-read-permission',
					'params' => [ 'test', 'Test' ]
				],
				'expectsException' => true
			],
			[
				'input' => 'Test',
				'options' => [
					'allowedNamespaces' => [ 1 ]
				],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-title-not-allowed-namespace',
					'params' => [ 'test', 'Test' ]
				],
				'expectsException' => true
			],
			[
				'input' => 'Test',
				'options' => [
					'blacklistedNamespaces' => [ 0 ]
				],
				'expected' => [
					'type' => 'error',
					'message' => 'inputprocessor-error-title-not-allowed-namespace',
					'params' => [ 'test', 'Test' ]
				],
				'expectsException' => true
			],
		];
	}
}
