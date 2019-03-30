<?php
$pdo=new PDO("mysql:host=localhost;dbname=caseMgt",'justin','@justin#94');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_SILENT);
$maleFirst=['Justin','John','Abraham','Samuel','Isaac','Emmanuel','Augustine','Bramuel','James','Joseph','Elias','Jojen','George','Vincent','Robert','Meshack','Dominic','Duncan','Dennis','Julius','Achills','Fidel','Peter','Bryan','Stephen','Steven','Collins','Eugene','Moses','Innocent','Charles','Kennedy','Shadrack','David','Saul','Solomon','Martin','Patrick','Albert','Siffrine','Pius','Crispinus','Michael','Joab','Jacob','Andrew','Stanis','Robbin','Geoffrey','Tommen','Sammy','Ramsay','Alex','Josphat','Bernard'];
$maleLast=['Wamalwa','Doe','Agoya','Wafula','Wanyama','Barasa','Wanyonyi','Otieno','Othieno','Maingi','Kiprono','Malel','Sifuna','Juma','Kioko','Musyoka','Waweru','Mwangi','Onyango','Ambetsa','Sakwa','Baraka','Sakula','Wekulo','Korir','Ruto','Rono','Okumu','Onyanja','Stark','Ndemange','Chisikwa','Madegwa','Oyango','Ambuthi'];
$femaleFirst=['Mary','Rose','Rachael','Purity','Pristine','Jane','Maria','Monica','Florence','Priscilla','Cersei','Catherine','Catelyne','Olenna','Sarah','Centrine','July','Juliet','Emmah','Immaculate','Josephine','Mercy','Esther','Lavincer','Priscah','Peris','Dorcas','Doreen','Dorah','Janet','Jacky','Jacklyne','Damaris','Phostine','Cecilia','Susan','Susanna','Beatrice','Pauline','Deborah','Everlyne','Lydia','Linet','Stacy','Tracy','Timina','Effie','Eddah','Sansa','Arya','Daenerys','Gilly','Veronica','Patricia','Millicent','Gertrude'];
$femaleLast=['Namalwa','Nanyama','Atieno','Rotich','Anyango','Nyambura','Nafuna','Amwayi','Athieno','Kathini','Jelimo','Kiplimo','Nasipwondi','Juma','Nthoki','Musyoki','Waweru','Mwangi','Onyango','Ambetsa','Sakwa','Baraka','Sakula','Wekulo','Korir','Ruto','Rono','Akumu','Anyanja','Stark','Ndemange','Chisikwa','Madegwa','Ayango','Ambuthi'];
$towns=['Nairobi','Bungoma','Mombasa','Kisumu','Eldoret','Kimilili','Nakuru','Naivasha','Meru','Nyeri','Mumias','Butere','Sabatia','Kanduyi','Luanda','Kakamega','Busia','Malaba','Teso','Garissa','Wajir'];
$gender=['Male','Female'];
$birthYearMin=1960;
$birthYearMax=1995;
/******************************CLIENTS********************************/
for ($i=0;$i<=15000;$i++) {
	$fname='';
	$lname='';
	$gend=$gender[random_int(0, 1)];
	if ($gend=='Male') {
		$fname=$maleFirst[random_int(0, count($maleFirst)-1)];
		$lname=$maleLast[random_int(0, count($maleLast)-1)];
	}else{
		$fname=$femaleFirst[random_int(0, count($femaleFirst)-1)];
		$lname=$femaleLast[random_int(0, count($femaleLast)-1)];
	}
	$dob=random_int($birthYearMin, $birthYearMax)."-".random_int(1, 12).'-'.random_int(1, 27);
	$email=strtolower($fname.$lname).random_int(0, 100).'@example.com';
	$address=random_int(1, 10000).' '.$towns[random_int(0, count($towns)-1)];
	$contact="2547".random_int(10000000, 19999999);
	$verified=random_int(0, 1);
	$pdo->query("INSERT INTO client VALUES(null,'{$fname}','','{$lname}','{$dob}','{$email}','{$address}','{$contact}',{$verified})");
	$password=password_hash('12345678',PASSWORD_DEFAULT);
	$pdo->query("INSERT INTO user VALUES('{$email}','{$password}','CLIENT')");
}
/******************************CLIENTS********************************/
/******************************EMPLOYEES********************************/
$universities=["Maseno University","University of Kabianga","University of Nairobi","Kenyatta University","JKUAT","Moi University","Harvard University","Cambridge University","Kibabii University"];
$colleges=["Sangalo Institute of Science and Technology","Kitale Polytechnic","Eldoret Polytechnic","Kisumu Polytechnic"];
$courses_uni=["LLB","Criminology","Forensics","Religion"];
$levels_uni=["Bachelors Degree","Masters Degree","PhD"];
$courses_college=["Catering","Hospitality"];
$levels_college=["Artisan","Certificate","Diploma","Higher Diploma"];
for ($i=0;$i<=250;$i++) {
	$fname='';
	$lname='';
	$gend=$gender[random_int(0, 1)];
	if ($gend=='Male') {
		$fname=$maleFirst[random_int(0, count($maleFirst)-1)];
		$lname=$maleLast[random_int(0, count($maleLast)-1)];
	}else{
		$fname=$femaleFirst[random_int(0, count($femaleFirst)-1)];
		$lname=$femaleLast[random_int(0, count($femaleLast)-1)];
	}
	$email=strtolower($fname.$lname).random_int(0, 100).'@example.com';
	$address=random_int(1, 10000).' '.$towns[random_int(0, count($towns)-1)];
	$empType="ADVOCATE";
	$salary=random_int(65000, 250000);
	$pdo->query("INSERT INTO employee VALUES(null,'{$fname}','','{$lname}','{$email}','{$address}','{$empType}',{$salary},null,'')");
	$password=password_hash('12345678',PASSWORD_DEFAULT);
	$pdo->query("INSERT INTO user VALUES('{$email}','{$password}','ADVOCATE')");
	$empId=1000000+$i;
	for ($j=1; $j <=5 ; $j++) {
		$contact="2547".random_int(10000000,19999999);
		$pdo->query("INSERT INTO employeeContact VALUES({$empId},'{$contact}')");
	}
	for ($k=1;$k<7;$k++) {
		$specialization=$courses_uni[random_int(0, count($courses_uni)-1)];
		$institution=$universities[random_int(0, count($universities)-1)];
		$achievement=$levels_uni[random_int(0, count($levels_uni)-1)];
		$year=random_int(1980, 2010);
		$pdo->query("INSERT INTO employeeEducation VALUES({$empId},'{$specialization}','{$institution}','{$achievement}',{$year},'')");
	}
	$salaryYear=random_int(1997, 2018);
	for ($l=1; $l < 20; $l++) { 
		$salaryYear=$salaryYear+1;
		$month=random_int(1, 12);
		for ($m=$salaryYear;$m<2019;$m++) {
			for ($n=1;$n<=12;$n++) {
				$datePayed=$year."-".$month."-"."27";
				$pdo->query("INSERT INTO payedSalary VALUES({$empId},{$salaryYear},{$n},'{$datePayed}',{$salary})");
			}
		}
	}
}
for ($i=0;$i<=50;$i++) {
	$fname='';
	$lname='';
	$gend=$gender[random_int(0, 1)];
	if ($gend=='Male') {
		$fname=$maleFirst[random_int(0, count($maleFirst)-1)];
		$lname=$maleLast[random_int(0, count($maleLast)-1)];
	}else{
		$fname=$femaleFirst[random_int(0, count($femaleFirst)-1)];
		$lname=$femaleLast[random_int(0, count($femaleLast)-1)];
	}
	$email=strtolower($fname.$lname).random_int(0, 100).'@example.com';
	$address=random_int(1, 10000).' '.$towns[random_int(0, count($towns)-1)];
	$empType="JUNIOR STAFF";
	$salary=random_int(30000, 38000);
	$pdo->query("INSERT INTO employee VALUES(null,'{$fname}','','{$lname}','{$email}','{$address}','{$empType}',{$salary},null,'')");
	for ($j=1; $j <=5 ; $j++) {
		$empId=1000000+$i;
		$contact="2547".random_int(10000000,19999999);
		$pdo->query("INSERT INTO employeeContact VALUES({$empId},'{$contact}')");
	}
	for ($k=1;$k<7;$k++) {
		$specialization=$courses_college[random_int(0, count($courses_college)-1)];
		$institution=$colleges[random_int(0, count($colleges)-1)];
		$achievement=$levels_college[random_int(2, count($levels_college)-1)];
		$year=random_int(1980, 2010);
		$pdo->query("INSERT INTO employeeEducation VALUES({$empId},'{$specialization}','{$institution}','{$achievement}',{$year},'')");
	}
	$salaryYear=random_int(1997, 2018);
	for ($l=1; $l < 20; $l++) { 
		$salaryYear=$salaryYear+1;
		$month=random_int(1, 12);
		for ($m=$salaryYear;$m<2019;$m++) {
			for ($n=1;$n<=12;$n++) {
				$datePayed=$year."-".$month."-"."27";
				$pdo->query("INSERT INTO payedSalary VALUES({$empId},{$salaryYear},{$n},'{$datePayed}',{$salary})");
			}
		}
	}
}
for ($i=0;$i<=20;$i++) {
	$fname='';
	$lname='';
	$gend=$gender[random_int(0, 1)];
	if ($gend=='Male') {
		$fname=$maleFirst[random_int(0, count($maleFirst)-1)];
		$lname=$maleLast[random_int(0, count($maleLast)-1)];
	}else{
		$fname=$femaleFirst[random_int(0, count($femaleFirst)-1)];
		$lname=$femaleLast[random_int(0, count($femaleLast)-1)];
	}
	$email=strtolower($fname.$lname).random_int(0, 100).'@example.com';
	$address=random_int(1, 10000).' '.$towns[random_int(0, count($towns)-1)];
	$empType="HELPER";
	$salary=random_int(15000, 20000);
	$pdo->query("INSERT INTO employee VALUES(null,'{$fname}','','{$lname}','{$email}','{$address}','{$empType}',{$salary},null,'')");
	for ($j=1; $j <=5 ; $j++) {
		$empId=1000000+$i;
		$contact="2547".random_int(10000000,19999999);
		$pdo->query("INSERT INTO employeeContact VALUES({$empId},'{$contact}')");
	}
	for ($k=1;$k<7;$k++) {
		$specialization=$courses_college[random_int(0, count($courses_college)-1)];
		$institution=$colleges[random_int(0, count($colleges)-1)];
		$achievement=$levels_college[random_int(0, 1)];
		$year=random_int(1980, 2010);
		$pdo->query("INSERT INTO employeeEducation VALUES({$empId},'{$specialization}','{$institution}','{$achievement}',{$year},'')");
	}
	$salaryYear=random_int(1997, 2018);
	for ($l=1; $l < 20; $l++) { 
		$salaryYear=$salaryYear+1;
		$month=random_int(1, 12);
		for ($m=$salaryYear;$m<2019;$m++) {
			for ($n=1;$n<=12;$n++) {
				$datePayed=$year."-".$month."-"."27";
				$pdo->query("INSERT INTO payedSalary VALUES({$empId},{$salaryYear},{$n},'{$datePayed}',{$salary})");
			}
		}
	}
}
/******************************EMPLOYEES********************************/
/******************************CASES********************************/
$caseTypes=["Murder and Manslaughter","Theft","Robbery","Rape","Economic sabbotage","Deformation of Character","Traffic","Drunk and Disorderly","Divorce and Adultery","Land and Property","Gender Violence","Domestic Violence","Forgery","Drugs and Trafficking","Smuggling","Business Law","Treason","Illigal Immigration","Riots"];
$caseTitles=[
	"Murder and Manslaughter" => ["Accused of Murdering a patient","Attempted suicide","Regicide","Patricide","Accused of killing a neighbour","Accused of killing police officer","Accused of Killing wife and children"],
	"Theft" => ["Stealing a handbag","Stealing of several cars","Land grabbing","Cattle rustling","Stealing power from KPLC"],
	"Robbery" => ["Invaded a store with fake guns","Attacked and stole from Mr. Doe", "Robbed a bank","Robbed a petrol station","Hijacked cars","Pirating"],
	"Rape" => ["Raped 12 year old","Raped neighbour","Attempted rape of Mrs. Doe","Rape of school girl","Rape and impregnation of teacher"],
	"Economic sabbotage" => ["NYS Season 1","NYS Season 2","Public land grabbing.","Loss of Ministry money","Maize scandle","Petrol sabbotage"],
	"Deformation of Character" => ["Publicly abused Mr. President","Called my neighbour a thief without proof","Made grave comment about a tribe","Called my grandma a witch","Verbally attacked a religion"],
	"Traffic" => ["Drunk driving","Hit a passenger","Knocked down a house with a trailer","Hit parked cars"],
	"Drunk and Disorderly" => ["Drunk and Disorderly"],
	"Divorce and Adultery" => ["Want to divorce my poor husband","Caught her cheating and it's over.","Slept with my someone else's hubby","Divorce"],
	"Land and Property" => ["Sold land that did not belong to me","Brother wants me out of family land","Developed other person's land","Chased out of personal property."],
	"Gender Violence" => ["Denied my daughters food","Awarded jobs to women only","Refused to employ women"],
	"Domestic Violence" => ["Wife beating","Husband beating","Parent beating","Child beating"],
	"Forgery" => ["Forged school letter","Fake academic certificates","Fake money","Faked employment letter","Fake ID Card","Fake academic certificates"],
	"Drugs and Trafficking" => ["Drugs and Trafficking"],
	"Smuggling" => ["Smuggling"],
	"Business Law" => ["Unfair competition","Goods sabbotage","Copyright Infringement","Misuse of Intellectual property"],
	"Treason" => ["Treason"],
	"Illigal Immigration" => ["Illigal Immigration"],
	"Riots" => ["Riots"]
];
$caseStatus=["ACCEPTED","REJECTED","PENDING"];
for ($i=0; $i < 4000; $i++) { 
	$caseID=1000000+$i;
	$caseType=$caseTypes[random_int(0, count($caseTypes)-1)];
	$caseTitle=$caseTitles[$caseType][random_int(0, count($caseTitles[$caseType])-1)];
	$courtName=$towns[random_int(0, count($towns)-1)]." Law Courts";
	$advocateID=random_int(1000000, 1000251);
	$year=random_int(1995, 2019);
	$month=random_int(1, 12);
	$day=random_int(1, 27);
	$firstHearing=$year."-".$month."-".$day;
	$status=$caseStatus[random_int(0, count($caseStatus)-1)];
	$clientID=random_int(1000000, 1015001);/**/
	$day=$day+2;
	$dateAdded=$year."-".$month."-".$day;
	$pdo->query("INSERT INTO appliedCases VALUES({$caseID},'{$caseTitle}','{$caseType}','{$courtName}',{$advocateID},'{$firstHearing}','{$status}',{$clientID},'','{$dateAdded}')");
	if ($status!='REJECTED' && $status!='PENDING') {
		$acceptedStatus=["CLOSED","IN PROGRESS"];
		$state=$acceptedStatus[random_int(0, 1)];
		$outcomes=["LOST","WON","WITHDRAWN"];
		if ($state=="CLOSED") {
			$outcome=$outcomes[random_int(0, count($outcomes)-1)];
		}else{
			$outcome='UNDECIDED';
		}
		$pdo->query("INSERT INTO cases VALUES({$caseID},'{$caseTitle}','{$caseType}','{$courtName}',{$advocateID},{$clientID},'{$firstHearing}','{$firstHearing}','{$state}','{$outcome}',null)");
		$pdo->query("INSERT INTO assignedCases VALUES ({$caseID},{$advocateID})");
		$cost=0;
		if ($caseType=="Murder and Manslaughter") {
			$cost=random_int(50000, 80000);
		}elseif($caseType=="Theft") {
			$cost=random_int(10000, 15000);
		}elseif($caseType=="Robbery") {
			$cost=random_int(15000, 25000);
		}elseif($caseType=="Rape") {
			$cost=random_int(40000, 75000);
		}elseif($caseType=="Economic sabbotage") {
			$cost=random_int(80000, 100000);
		}elseif($caseType=="Deformation of Character") {
			$cost=random_int(10000, 30000);
		}elseif($caseType=="Traffic") {
			$cost=random_int(10000, 30000);
		}elseif($caseType=="Drunk and Disorderly") {
			$cost=random_int(10000, 30000);
		}elseif($caseType=="Divorce and Adultery") {
			$cost=random_int(10000, 30000);
		}elseif($caseType=="Land and Property") {
			$cost=random_int(15000, 35000);
		}elseif($caseType=="Gender Violence") {
			$cost=random_int(10000, 30000);
		}elseif($caseType=="Domestic Violence") {
			$cost=random_int(10000, 30000);
		}elseif($caseType=="Forgery") {
			$cost=random_int(20000, 30000);
		}elseif($caseType=="Drugs and Trafficking") {
			$cost=random_int(200000, 300000);
		}elseif($caseType=="Smuggling") {
			$cost=random_int(10000, 30000);
		}elseif($caseType=="Business Law") {
			$cost=random_int(30000, 35000);
		}elseif($caseType=="Treason") {
			$cost=random_int(80000, 150000);
		}elseif($caseType=="Illigal Immigration") {
			$cost=random_int(20000, 40000);
		}elseif($caseType=="Riots") {
			$cost=random_int(20000, 30000);
		}else{
			$cost=20000;
		}
		$pdo->query("INSERT INTO caseCost VALUES({$caseID},{$cost})");
		$maxWitnesses=random_int(0, 20);
		for ($z=0; $z < $maxWitnesses; $z++) { 
			$name='';
			$gen=$gender[random_int(0, 1)];
			if ($gen=="Male") {
				$name=$maleFirst[random_int(0, count($maleFirst)-1)].' '.$maleLast[random_int(0, count($maleLast)-1)];
			}else{
				$name=$femaleFirst[random_int(0, count($femaleFirst)-1)].' '.$femaleLast[random_int(0, count($femaleLast)-1)];
			}
			$address=random_int(1, 2000).' '.$towns[random_int(0, count($towns)-1)];
			$contact="2547".random_int(10000000, 19999999);
			$email=str_replace(' ', '', strtolower($name)).'@example.com';
			$pdo->query("INSERT INTO caseWitness VALUES(null,{$caseID},'{$name}','{$address}','{$contact}','{$email}')");
		}
		$seen=random_int(0, 1);
		$pdo->query("INSERT INTO clientNotification VALUES(null,{$clientID},{$caseID},'Your case has been accepted for defence. Please make the necessary arrangements and payments',{$seen},null)");
		$transID="GFAGTSHA".random_int(1000, 10000);
		$amount=random_int(1000, $cost);
		$pdo->query("INSERT INTO payment VALUES(null,{$caseID},null,'{$transID}','CONFIRMED',{$amount})");
	}else{
		$seen=random_int(0, 1);
		$pdo->query("INSERT INTO clientNotification VALUES(null,{$clientID},{$caseID},'Your case has been rejected for defence.',{$seen},null)");
	}
}
/******************************CASES********************************/