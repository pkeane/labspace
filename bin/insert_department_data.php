<?php


include 'config.php';

$data = json_decode(file_get_contents('depts.json'),1);

foreach ($data as $office) {
	$set[$office['deans_code']] = $office;
}

print "\nNot on Geoff's List:\n\n";

$d = new Dase_DBO_Dept($db);
foreach ($d->findAll(1) as $o) {
	$set2[$o->ascii_id] = $o;
	if (isset($set[$o->ascii_id])) {
		$json_office = $set[$o->ascii_id];
		$orig = clone($o);
		$o->phone = $json_office['phone']; 
		$o->address = $json_office['address']; 
		$o->chair_name = $json_office['depthead_name']; 
		$o->chair_email = $json_office['depthead_email']; 
		$o->chair_eid = '??????'; 
		if ($orig->chair_name != $o->chair_name) {
			print "$o->name CHANGED CHAIR: was: $orig->chair_name now: $o->chair_name\n";
		}
		//print "got $o->name\n";
	} else {
		//print "$o->name ($o->ascii_id)\n";
	}
}

print "\nnot in labspace db:\n\n";

foreach ($set as $key => $val) {
	if (!isset($set2[$key])) {
		//print $val['name']."\n";
	}
}
