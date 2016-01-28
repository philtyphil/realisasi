<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function pluck ( $a, $prop )
{
	$out = array();

	for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
		$out[] = $a[$i][$prop];
	}

	return $out;
}
function dt_where($request,$columns)	
{
	
}