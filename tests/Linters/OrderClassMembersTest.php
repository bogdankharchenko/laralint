<?php

namespace Glhd\LaraLint\Tests\Linters;

use Galahad\LaraLint\Tests\TestCase;
use Glhd\LaraLint\Linters\OrderClassMembers;

class OrderClassMembersTest extends TestCase
{
	public function test_it_allows_code_in_the_expected_order() : void
	{
		$source = <<<'END_SOURCE'
		class Foo
		{
			use Bar;
			
			public const FOO = 1;
			protected const BAR = 1;
			private const BAZ = 1;
			
			public static $static_foo = 1;
			protected static $static_bar = 1;
			private static $static_baz = 1;
			
			public $foo = 1;
			protected $bar = 1;
			private $baz = 1;
			
			abstract public function abstractFoo();
			
			public static function staticFoo()
			{
				return static::$static_foo;
			}
			
			protected static function staticBar()
			{
				return static::$static_bar;
			}
			
			private static function staticBaz()
			{
				return static::$static_baz;
			}
			
			public function __construct()
			{
				$this->foo = 2;
			}
			
			public function foo()
			{
				return $this->foo;
			}
			
			protected function bar()
			{
				return $this->bar;
			}
			
			private function baz()
			{
				return $this->baz;
			}
		}
		END_SOURCE;
		
		$this->withLinter(OrderClassMembers::class)
			->lintSource($source)
			->assertNoLintingResults();
	}
	
	public function test_it_gives_special_consideration_to_setUp_and_tearDown_methods_in_tests() : void
	{
		$source = <<<'END_SOURCE'
		class FooTest
		{
			protected function setUp() : void
			{
			}
			
			protected function tearDown() : void
			{
			}
			
			public function publicFunctionAfterProtectedFunction()
			{
			}
		}
		END_SOURCE;
		
		$this->withLinter(OrderClassMembers::class)
			->lintSource($source)
			->assertNoLintingResults();
	}
	
	public function test_it_handles_anonymous_classes_with_their_own_ordering() : void
	{
		$source = <<<'END_SOURCE'
		class Foo
		{
			public function first()
			{
			}
			
			protected function second()
			{
				$anonymous = new class {
					public function thisIsOk()
					{
						return true;
					}
				};
			}
		}
		END_SOURCE;
		
		$this->withLinter(OrderClassMembers::class)
			->lintSource($source)
			->assertNoLintingResults();
	}
	
	public function test_it_warns_of_improper_ordering() : void
	{
		$source = <<<'END_SOURCE'
		class Foo
		{
			protected function first()
			{
			}
			
			public function second()
			{
			}
		}
		END_SOURCE;
		
		$this->withLinter(OrderClassMembers::class)
			->lintSource($source)
			->assertLintingResult('public method should not come after a protected method', true);
	}
}
