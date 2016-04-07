<?php

namespace Routeria;

class Translator
{
	const INT = '[0-9]+';
	const ALPHA = '[a-zA-Z_-]+';
	const WORD = '[a-zA-Z_-]+';
	const ALNUM = '[a-zA-Z0-9_-]+';
	const HEX = '[0-9A-F]+';
	const ALL = '.+';

	public static function translate($string)
	{
		if (!defined(__CLASS__ . '::' . strtoupper($string))) {
			throw new \RuntimeException(sprintf('Translation not found for %s', $string));
		}
		else
		{
			return constant(__CLASS__ . '::' . strtoupper($string));
		}
	}
}