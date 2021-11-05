<?php

/**
 * @file
 * Contains \FastFrame\Utility\NestedArrayHelperTest
 */

namespace FastFrame\Utility;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the NestedArrayHelper class
 *
 * @package FastFrame\Utility
 */
class NestedArrayHelperTest
	extends TestCase
{
	private $aryTester = [
		'some' => [
			'where' => [
				'over' => [
					'the' => [
						'rainbow' => 'blue birds fly'
					]
				]
			]
		]
	];

	public function testSeparatorChanges()
	{
		NestedArrayHelper::setSeparator('/');
		self::assertEquals(
			'blue birds fly',
			NestedArrayHelper::get($this->aryTester, 'some/where/over/the/rainbow')
		);
		NestedArrayHelper::setSeparator();
	}

	public function testSeparatorIsReset()
	{
		NestedArrayHelper::nextSeparator('/');
		self::assertEquals(
			'blue birds fly',
			NestedArrayHelper::get($this->aryTester, 'some/where/over/the/rainbow')
		);
		self::assertEquals(
			'blue birds fly',
			NestedArrayHelper::get($this->aryTester, 'some.where.over.the.rainbow')
		);
	}

	public function testGetReturnsWithStringPath()
	{
		$s = NestedArrayHelper::get($this->aryTester, 'some.where.over.the.rainbow');
		self::assertEquals('blue birds fly', $s);
	}

	public function testGetReturnsWithArrayPath()
	{
		self::assertEquals(
			'blue birds fly', NestedArrayHelper::get(
			$this->aryTester, [
			'some',
			'where',
			'over',
			'the',
			'rainbow'
		]));
	}

	public function testGetReturnsDefaultWhenPathNotFound()
	{
		$ary = [];
		$a = NestedArrayHelper::get($ary, ['some', 'where', 'over', 'the', 'rainbow'], 'uh oh');
		self::assertEquals('uh oh', $a);
	}

	public function testSetWithStringPath()
	{
		$ary = [];
		NestedArrayHelper::set($ary, 'some.where.over.the.rainbow', 'blue birds fly');
		self::assertEquals($this->aryTester, $ary);
	}

	public function testSetWithArrayPath()
	{
		$ary = [];
		NestedArrayHelper::set($ary, ['some', 'where', 'over', 'the', 'rainbow'], 'blue birds fly');
		self::assertEquals($this->aryTester, $ary);
	}

	public function testSetDoesNotClobberExistingKeys()
	{
		$ary                                                 = $this->aryTester;
		$ary['some']['where']['over']['the']['wagon']        = 'wheel';
		$expected                                            = $ary;
		$expected['some']['where']['over']['the']['rainbow'] = 'blue birds fly high';

		NestedArrayHelper::set($ary, ['some', 'where', 'over', 'the', 'rainbow'], 'blue birds fly high');
		self::assertEquals($expected, $ary);
	}

	public function testHasReturnsFalse()
	{
		$ary = [];

		self::assertFalse(NestedArrayHelper::has($ary, 'superman.is'));
	}

	public function testHasReturnTrue()
	{
		self::assertTrue(NestedArrayHelper::has($this->aryTester, ['some', 'where', 'over']));
	}

	public function testDeepMergeMergesWithTwoArray()
	{
		$a1 = [
			'super' => [
				'duper' => [
					'woot' => 'kakaw'
				]
			],
			'some'  => [
				'where' => [
					'over' => [
						'the' => [
							'wagon' => 'wheel'
						]
					]
				]
			]
		];

		$e                                            = $a1;
		$e['some']['where']['over']['the']['rainbow'] = 'blue birds fly';

		self::assertEquals($e, NestedArrayHelper::deepMerge($a1, $this->aryTester));
	}

	public function testDeepMergeMergesWithMultipleArray()
	{
		$a1 = [
			'super' => [
				'duper' => [
					'woot' => 'kakaw'
				]
			],
			'some'  => [
				'where' => [
					'over' => [
						'the' => [
							'wagon' => 'wheel'
						]
					]
				]
			]
		];
		$a2 = [
			'boom' => [
				'to' => 'square'
			]
		];

		$e                                            = $a1;
		$e['some']['where']['over']['the']['rainbow'] = 'blue birds fly';
		$e['boom']['to']                              = 'square';

		self::assertEquals($e, NestedArrayHelper::deepMerge($a1, $this->aryTester, $a2));
	}

	public function testExpand()
	{
		$ary = [
			'some.where.over.the.rainbow' => 'blue birds fly',
			'some.where.over.the.wagon' => 'wheel',
			'some.where.else' => 'something booms',
			'boom.to' => 'square'
		];
		$e = [
			'some'  => [
				'where' => [
					'else' => 'something booms',
					'over' => [
						'the' => [
							'wagon' => 'wheel',
							'rainbow' => 'blue birds fly'
						]
					]
				]
			],
			'boom' => [
				'to' => 'square'
			]
		];
		self::assertEquals($e, NestedArrayHelper::expand($ary));

	}

	public function testCompress()
	{
		$e = [
			'some.where.over.the.rainbow' => 'blue birds fly',
			'some.where.over.the.wagon' => 'wheel',
			'some.where.else' => 'something booms',
			'boom.to' => 'square'
		];
		$ary = [
			'some'  => [
				'where' => [
					'else' => 'something booms',
					'over' => [
						'the' => [
							'wagon' => 'wheel',
							'rainbow' => 'blue birds fly'
						]
					]
				]
			],
			'boom' => [
				'to' => 'square'
			]
		];
		self::assertEquals($e, NestedArrayHelper::compress($ary));
	}
}