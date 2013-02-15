<?php

function getStars($stars) {
	$starcount = $stars->count();
	$type = pq($stars->get(0))->attr('src');
	preg_match('|_([a-z]+)\.[a-z]+$|', $type, $match);
	$type = $match[1];
	$r = new stdClass();
	$r->type = $type;
	$r->count = $starcount;
	return $r;
}
