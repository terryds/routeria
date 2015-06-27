<?php

namespace Regex;

class Translator implements TranslatorInterface
{
	const INT = '[0-9]+';
	const ALPHA = '[a-zA-Z_-]+';
	const ALNUM = '[a-zA-Z0-9_-]+';

	public function translate($string)
	{
		if (!is_string($string)) {
			throw new \InvalidArgumentException();
		}
		if (!defined(__CLASS__ . '::' . strtoupper($string))) {
			return $string;
		}
		else
		{
			return constant(__CLASS__ . '::' . strtoupper($string));
		}
	}
}