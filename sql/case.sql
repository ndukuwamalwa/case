DROP DATABASE IF EXISTS caseMgt;
CREATE DATABASE caseMgt;
USE caseMgt;

CREATE TABLE IF NOT EXISTS admin(
	advocateID INT NOT NULL AUTO_INCREMENT,
	fname VARCHAR(40) NOT NULL,
	mname VARCHAR(40) NULL,
	lname VARCHAR(40) NOT NULL,
	email VARCHAR(60) NOT NULL,
	address VARCHAR(70) NOT NULL,
	dob DATE NOT NULL,
	photo MEDIUMBLOB NULL,
	mime VARCHAR(255) NULL,
	PRIMARY KEY(advocateID),
	UNIQUE(email),
	INDEX(email),
	INDEX(fname),
	INDEX(lname),
	INDEX(address)
)Engine=InnoDB;
ALTER TABLE admin AUTO_INCREMENT=1000000;
INSERT INTO admin(fname,lname,email,address,dob) VALUES('Justin','Wamalwa','justinwamalwa3@gmail.com','2487 Bungoma','1994-08-20');

CREATE TABLE IF NOT EXISTS adminContact(
	advocateID INT NOT NULL,
	contact INT NOT NULL,
	CONSTRAINT FOREIGN KEY(advocateID) REFERENCES admin(advocateID) ON DELETE CASCADE ON UPDATE CASCADE,
	UNIQUE(contact)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS adminEducation(
	advocateID INT NOT NULL,
	specialization VARCHAR(90) NOT NULL,
	institution VARCHAR(80) NOT NULL,
	achievement VARCHAR(50) NOT NULL,
	year INT NOT NULL CHECK(year>1950),
	description TEXT NOT NULL,
	CONSTRAINT FOREIGN KEY(advocateID) REFERENCES admin(advocateID) ON DELETE CASCADE ON UPDATE CASCADE
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS employee(
	empID INT NOT NULL AUTO_INCREMENT,
	fname VARCHAR(40) NOT NULL,
	mname VARCHAR(40) NOT NULL,
	lname VARCHAR(40) NOT NULL,
	email VARCHAR(40) NOT NULL,
	address VARCHAR(70) NOT NULL,
	empType ENUM("ADVOCATE","JUNIOR STAFF","HELPER") NOT NULL,
	salary DOUBLE NOT NULL,
	photo BLOB NULL,
	mime VARCHAR(255) NULL,
	PRIMARY KEY(empID,email),
	INDEX(fname),
	INDEX(lname),
	INDEX(address),
	INDEX(empType),
	INDEX(salary),
	UNIQUE(email)
)Engine=InnoDB;
ALTER TABLE employee AUTO_INCREMENT=1000000;
CREATE TABLE IF NOT EXISTS employeeEducation(
	empID INT NOT NULL,
	specialization VARCHAR(90) NOT NULL,
	institution VARCHAR(80) NOT NULL,
	achievement VARCHAR(50) NOT NULL,
	year INT NOT NULL CHECK(year>1950),
	description TEXT NOT NULL,
	CONSTRAINT FOREIGN KEY(empID) REFERENCES employee(empID) ON DELETE CASCADE ON UPDATE CASCADE,
	UNIQUE(empID,specialization,institution,achievement)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS payedSalary(
	empID INT NOT NULL,
	year INT NOT NULL,
	month INT NOT NULL,
	datePayed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	salary DOUBLE NOT NULL,
	CONSTRAINT FOREIGN KEY(empID) REFERENCES employee(empID),
	INDEX(datePayed),
	INDEX(salary),
	UNIQUE(empID,year,month)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS employeeContact(
	empID INT NOT NULL,
	contact VARCHAR(15) NOT NULL,
	CONSTRAINT FOREIGN KEY(empID) REFERENCES employee(empID) ON DELETE CASCADE ON UPDATE CASCADE,
	UNIQUE(empID, contact)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS client(
	clientID INT NOT NULL AUTO_INCREMENT,
	fname VARCHAR(40) NOT NULL,
	mname VARCHAR(40) NULL,
	lname VARCHAR(40) NOT NULL,
	dob DATE NOT NULL,
	email VARCHAR(40) NOT NULL,
	address VARCHAR(70) NOT NULL,
	contact VARCHAR(14) NOT NULL,
	verified BOOLEAN NOT NULL,
	PRIMARY KEY (clientID,email),
	INDEX(fname),
	INDEX(lname),
	INDEX(address),
	UNIQUE(email),
	UNIQUE(contact)
)Engine=InnoDB;
ALTER TABLE client AUTO_INCREMENT=1000000;
CREATE TABLE IF NOT EXISTS user(
	username VARCHAR(60) NOT NULL CHECK(username IN (SELECT email FROM admin) OR username IN (SELECT email FROM employee) OR username IN (SELECT email
		FROM client)),
	password VARCHAR(160) NOT NULL,
	type ENUM("CLIENT","ADVOCATE","ADMIN"),
	INDEX(type),
	PRIMARY KEY(username)
)Engine=InnoDB;
INSERT INTO user VALUES('justinwamalwa3@gmail.com','$2y$10$lJeZ6suwF2CPhuLoAACSYO0idQnMUDjnHt998Cf0hTveTiFObnRlG','ADMIN');
CREATE TABLE IF NOT EXISTS appliedCases(
	id INT NOT NULL AUTO_INCREMENT,
	caseTitle VARCHAR(100) NOT NULL,
	caseType VARCHAR(40) NOT NULL,
	courtName VARCHAR(50) NOT NULL,
	advocateID INT NOT NULL,
	firstHearing DATETIME NOT NULL,
	status ENUM("ACCEPTED","REJECTED","PENDING") NOT NULL,
	clientID INT NOT NULL,
	description TEXT NOT NULL,
	dateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT FOREIGN KEY(clientID) REFERENCES client(clientID) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT FOREIGN KEY(advocateID) REFERENCES employee(empID) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY(id),
	INDEX(caseType),
	INDEX(caseTitle),
	INDEX(courtName),
	INDEX(firstHearing),
	INDEX(status),
	FULLTEXT(description)
)Engine=InnoDB;
ALTER TABLE appliedCases AUTO_INCREMENT=1000000;
CREATE TABLE IF NOT EXISTS cases(
	caseID INT NOT NULL,
	caseTitle VARCHAR(100) NOT NULL,
	caseType VARCHAR(40) NOT NULL,
	courtName VARCHAR(50) NOT NULL,
	advocateID INT NOT NULL,
	clientID INT NOT NULL,
	lastHearing DATETIME NOT NULL,
	nextHearing DATETIME NOT NULL,
	status ENUM("CLOSED","IN PROGRESS") NOT NULL,
	outcome ENUM("LOST","WON","APPEALED","WITHDRAWN","UNDECIDED") NOT NULL,
	dateAccepted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(caseID),
	CONSTRAINT FOREIGN KEY(caseID) REFERENCES appliedCases(id),
	CONSTRAINT FOREIGN KEY(advocateID) REFERENCES appliedCases(advocateID),
	CONSTRAINT FOREIGN KEY(clientID) REFERENCES client(clientID),
	INDEX(caseType),
	INDEX(courtName),
	INDEX(advocateID),
	INDEX(lastHearing),
	INDEX(nextHearing),
	INDEX(status),
	INDEX(outcome)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS assignedCases(
	caseID INT NOT NULL,
	advocateID INT NOT NULL,
	UNIQUE(caseID, advocateID),
	CONSTRAINT FOREIGN KEY(caseID) REFERENCES cases(caseID),
	CONSTRAINT FOREIGN KEY(advocateID) REFERENCES cases(advocateID),
	INDEX(caseID),
	INDEX(advocateID)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS caseDetails(
	caseID INT NOT NULL,
	hearingDate DATETIME NOT NULL,
	proceedings TEXT NOT NULL,
	CONSTRAINT FOREIGN KEY(caseID) REFERENCES cases(caseID) ON DELETE CASCADE ON UPDATE CASCADE,
	INDEX(hearingDate),
	FULLTEXT(proceedings)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS caseCost(
	caseID INT NOT NULL,
	cost DOUBLE NOT NULL,
	CONSTRAINT FOREIGN KEY(caseID) REFERENCES cases(caseID),
	PRIMARY KEY (caseID)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS caseWitness(
	id INT NOT NULL AUTO_INCREMENT,
	caseID INT NOT NULL,
	witnessName VARCHAR(120) NOT NULL,
	address VARCHAR(70) NOT NULL,
	contact VARCHAR(15) NOT NULL,
	email VARCHAR(60) NOT NULL,
	PRIMARY KEY(id),
	CONSTRAINT FOREIGN KEY(caseID) REFERENCES cases(caseID) ON DELETE CASCADE ON UPDATE CASCADE,
	INDEX(witnessName),
	INDEX(address),
	INDEX(contact),
	INDEX(email)
)Engine=InnoDB;
CREATE TABLE IF NOT EXISTS meetingsArranged(
	id INT NOT NULL AUTO_INCREMENT,
	advocateID INT NOT NULL,
	clientID INT NOT NULL,
	caseID INT NOT NULL,
	meetingDate DATETIME NOT NULL,
	venue VARCHAR(50) NOT NULL,
	status ENUM("DONE","AWAITING","CANCELLED") NOT NULL,
	PRIMARY KEY(id),
	CONSTRAINT FOREIGN KEY(advocateID) REFERENCES cases(advocateID),
	CONSTRAINT FOREIGN KEY(clientID) REFERENCES cases(clientID) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT FOREIGN KEY (caseID) REFERENCES cases(caseID),
	INDEX(meetingDate),
	INDEX(venue),
	INDEX(status)
)Engine=InnoDB;
ALTER TABLE caseWitness AUTO_INCREMENT=1000000;
CREATE TABLE IF NOT EXISTS payment(
	paymentID INT NOT NULL AUTO_INCREMENT,
	caseID INT NOT NULL,
	datePayed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	transID VARCHAR(40) NOT NULL,
	status ENUM("PENDING","CONFIRMED", "FAILED") NOT NULL,
	amount DOUBLE NOT NULL,
	PRIMARY KEY(paymentID),
	CONSTRAINT FOREIGN KEY(caseID) REFERENCES cases(caseID) ON DELETE CASCADE ON UPDATE CASCADE,
	INDEX(datePayed),
	UNIQUE(transID),
	INDEX(transID),
	INDEX(status)
)Engine=InnoDB;
ALTER TABLE payment AUTO_INCREMENT=100000000;
CREATE TABLE IF NOT EXISTS clientNotification(
	id INT NOT NULL AUTO_INCREMENT,
	clientID INT NOT NULL,
	caseID INT NOT NULL,
	message TEXT NOT NULL,
	seen BOOLEAN NOT NULL DEFAULT 0,
	dateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(id),
	CONSTRAINT FOREIGN KEY(clientID) REFERENCES client(clientID),
	CONSTRAINT FOREIGN KEY(caseID) REFERENCES cases(caseID),
	FULLTEXT(message),
	INDEX(seen),
	INDEX(dateAdded)
)Engine=InnoDB;