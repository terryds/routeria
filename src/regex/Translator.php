<?php

namespace Regex;

class Translator implements TranslatorInterface
{
	const INT = '[0-9]+';
	const ALPHA = '[a-zA-Z_-]+';
	const ALNUM = '[a-zA-Z0-9_-]+';

	public static function translate($string)
	{
		if (!defined(__CLASS__ . '::' . strtoupper($string))) {
			throw new RuntimeException(sprintf('Translation not found for %s', $string));
		}
		else
		{
			return constant(__CLASS__ . '::' . strtoupper($string));
		}
	}
}