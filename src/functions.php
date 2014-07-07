<?php

function get_longest_common_subsequence($string_1, $string_2) {
	$string_1_length = strlen($string_1);
	$string_2_length = strlen($string_2);
	$return          = "";
 
	if ($string_1_length === 0 || $string_2_length === 0)
	{
		// No similarities
		return $return;
	}
 
	$longest_common_subsequence = array();
 
	// Initialize the CSL array to assume there are no similarities
	for ($i = 0; $i < $string_1_length; $i++)
	{
		$longest_common_subsequence[$i] = array();
		for ($j = 0; $j < $string_2_length; $j++)
		{
			$longest_common_subsequence[$i][$j] = 0;
		}
	}
 
	$largest_size = 0;
 
	for ($i = 0; $i < $string_1_length; $i++)
	{
		for ($j = 0; $j < $string_2_length; $j++)
		{
			// Check every combination of characters
			if ($string_1[$i] === $string_2[$j])
			{
				// These are the same in both strings
				if ($i === 0 || $j === 0)
				{
					// It's the first character, so it's clearly only 1 character long
					$longest_common_subsequence[$i][$j] = 1;
				}
				else
				{
					// It's one character longer than the string from the previous character
					$longest_common_subsequence[$i][$j] = $longest_common_subsequence[$i - 1][$j - 1] + 1;
				}
 
				if ($longest_common_subsequence[$i][$j] > $largest_size)
				{
					// Remember this as the largest
					$largest_size = $longest_common_subsequence[$i][$j];
					// Wipe any previous results
					$return       = "";
					// And then fall through to remember this new value
				}
 
				if ($longest_common_subsequence[$i][$j] === $largest_size)
				{
					// Remember the largest string(s)
					$return = substr($string_1, $i - $largest_size + 1, $largest_size);
				}
			}
			// Else, $CSL should be set to 0, which it was already initialized to
		}
	}
 
	// Return the list of matches
	return $return;
}

function delTree($dir) {
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

function curry($fn) {
	$args = array_slice(func_get_args(), 1);
	return function() use(&$fn, &$args) {
		$new_args = func_get_args();
		$final_args = array();
		foreach ($args as &$arg) {
			if ($arg === null and count($new_args) > 0) {
				$final_args[] = array_shift($new_args);
			}
			else {
				$final_args[] = $arg;
			}
		}
		return call_user_func_array($fn, $final_args);
	};
}

function slug($s, $separator="-") {
	return trim(preg_replace('/[^a-zA-Z0-9]+/', $separator, strtolower($s)), $separator);
}
