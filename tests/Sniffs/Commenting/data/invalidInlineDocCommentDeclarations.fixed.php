<?php

class Foo
{

	/** @var string */
	private $foo;

	public function __construct()
	{
		/** @var string[] $a */
		$a = $this->get();

		/** @see https://www.slevomat.cz */
		$b = null;

		/** @var $c */
		$c = [];

		/** @var iterable|array|\Traversable $d Lorem ipsum */
		$d = [];

		/** @var string $f */
		foreach ($e as $f) {

		}

		/** @var \DateTimeImmutable $h */
		while ($h = current($g)) {

		}

		/** @var string $i */
		$i = 'i';

		/** @var */
		$j = 10;
	}

	public function get()
	{
		$a = [];
		return $a;
	}

}
