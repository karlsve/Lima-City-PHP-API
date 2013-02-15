<?php

function getRole($role) {
	return substr($role, 0, strpos($role, ',') !== false ? strpos($role, ',') : strlen($role));	
}
