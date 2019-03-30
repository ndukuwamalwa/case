'use strict';
import {Ajax} from "./ajax.js";
import {Utils} from "./utils.js";
import {Table} from "./table.js";

const endPoint="../server/services/advocate.php";
const ajax=new Ajax(endPoint);
function dashboard()
{
	ajax.operate({service : "stats"})
	.then(stats=>{
		for (let key of Object.keys(stats)) {
			let td=document.getElementById("stat-"+key);
			if (td!==null) {
				td.innerHTML=stats[key];
			}
		}
	})
}
document.getElementById("dash").addEventListener('click', function(event){
	dashboard();
});
window.onload=function(){
	if (window.location.hash==="" || window.location.hash==="#dashboard") {
		dashboard();
	}
};
(function profile(){
	document.getElementById('view-profile-btn').addEventListener('click', function(event){
		ajax.operate({service : "viewProfile"})
		.then(json=>{
			let data=json[0];
			let parent=document.getElementById('view-profile-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Attribute","Value"])
			.addRow([{text : "Advocate ID"},{text : data.empID}])
			.addRow([{text : "Full name"},{text : data.name}])
			.addRow([{text : "Email address"},{text : data.email}])
			.addRow([{text : "Physical address"},{text : data.address}])
			.addRow([{text : "Salary"},{text : data.salary}])
			.appendOn(parent);
		})
	});
	Utils.processForm(endPoint,document.getElementById('add-contact-form'),['contact'],'add-contact','addContact', true);
	document.getElementById('view-contacts-btn').addEventListener('click', function(event){
		ajax.operate({service : "viewContact"})
		.then(json=>{
			let contacts=json;
			let parent=document.getElementById('view-contacts-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Contact","Action"])
			for (let contact of contacts) {
				table.addRow([{text : contact.contact, styles : { textAlign : "center"}},{text : "<i class='fa fa-trash'></i>", styles : {color: "red", 
					cursor : "pointer", textAlign : "center"}, onclick : function(row){
						ajax.operate({service : "deleteContact", "contact" : contact.contact})
						.then(json=>{
							Utils.showMessage(json);
							if (json.status==="success") {
								row.style.display="none";
							}
						})
					}}])
			}
			table.appendOn(parent);
		})
	});
	Utils.processForm(endPoint,document.getElementById('add-academic-qualification-form'),['specialization','institution','achievement','year','description'],
		'add-education','addEducation', true);
	document.getElementById('view-education-btn').addEventListener('click', function(event){
		ajax.operate({service : "viewEducation"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('view-education-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Specialization","Institution","Award","Year of Award","Notes"])
			for (let item of data) {
				table.addRow([{text : item.specialization},{text : item.institution},{text : item.achievement},{text : item.year},{text : item.description}]);
			}
			table.appendOn(parent);
		})
	});
})();
(function cases(){
	document.getElementById('applied-case-btn').addEventListener('click', function(event){
		ajax.operate({service : "appliedCases"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('applied-case-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Title","Client","Case Type","Court","Suggested advocate","First Hearing","Date applied","Description"])
			for (let item of data) {
				table.addRow([{text : item.title},{text : item.client},{text : item.caseType},{text : item.courtName},{text : item.advocate},
				{text: item.firstHearing},{text : item.dateApplied},{text : item.description}]);
			}
			table.appendOn(parent);
		})
	});
	Utils.processSearch(endPoint,document.getElementById('view-case-form'),"searchCase",keyword=>{
		ajax.operate({service : "viewCase",id : keyword})
		.then(json=>{
			let mainDetails=JSON.parse(json.mainDetails)[0];
			let clientDetails=JSON.parse(json.clientDetails)[0];
			let assignmentDetails=JSON.parse(json.assignmentDetails)[0];
			let payments=JSON.parse(json.payments);
			let meetings=JSON.parse(json.meetings);
			let witnesses=JSON.parse(json.witnesses);
			let proceedings=JSON.parse(json.proceedings);
			let parent=document.getElementById('view-case-form').parentElement;
			let container=document.createElement('div');
			Utils.clearPrevResults(parent,1);
			let h2=document.createElement("h2");
			h2.innerHTML="CASE DETAILS";
			container.appendChild(h2);
			let table=new Table();
			table.header(["Case ID","Case Title","Case Type","Court","Advocate ID","Client ID","Last Hearing", "Next Hearing",
				"Status","Outcome","Date accepted"])
			table.addRow([{text : mainDetails.caseID},{text : mainDetails.caseTitle},{text : mainDetails.caseType},{text : mainDetails.courtName},
				{text : mainDetails.advocateID},{text : mainDetails.clientID},{text : mainDetails.lastHearing},{text : mainDetails.nextHearing},
				{text : mainDetails.status},{text : mainDetails.outcome},{text : mainDetails.dateAccepted}]);
			table.appendOn(container);
			h2=document.createElement("h2");
			h2.innerHTML="Client Details";
			container.appendChild(h2);
			table=new Table();
			table.header(["Name","Email address","Contact"])
			.addRow([{text : clientDetails.name},{text : clientDetails.email},{text : clientDetails.contact}])
			.appendOn(container);
			h2=document.createElement("h2");
			h2.innerHTML="Assignment Details";
			container.appendChild(h2);
			table=new Table();
			table.header(["Advocate name","Email address"])
			.addRow([{text : assignmentDetails.name},{text : assignmentDetails.contact}])
			.appendOn(container);
			h2=document.createElement("h2");
			h2.innerHTML="Payment Details";
			container.appendChild(h2);
			table=new Table();
			table.header(["Payment ID","Date paid","Transaction ID","Status","Amount"])
			for (let payment of payments) {
				table.addRow([{text : payment.paymentID},{text : payment.datePayed},{text : payment.transID},{text : payment.status},{text : payment.amount}]);
			}
			table.appendOn(container);
			h2=document.createElement("h2");
			h2.innerHTML="Case Meetings";
			container.appendChild(h2);
			table=new Table();
			table.header(["Meeting Date","Venue"])
			for (let meeting of meetings) {
				table.addRow([{text : meeting.meetingDate},{text : meeting.venue}]);
			}
			table.appendOn(container);
			h2=document.createElement("h2");
			h2.innerHTML="Witnesses";
			container.appendChild(h2);
			table=new Table();
			table.header(["Witness Name","Address","Email Address","Contact"])
			for (let witness of witnesses) {
				table.addRow([{text : witness.witnessName},{text : witness.address},{text : witness.email},{text : witness.contact}]);
			}
			table.appendOn(container);
			h2=document.createElement("h2");
			h2.innerHTML="Court Proceedings";
			container.appendChild(h2);
			table=new Table();
			table.header(["Date","Proceeding"])
			for (let proceeding of proceedings) {
				table.addRow([{text : proceeding.hDate},{text : proceeding.proceedings}]);
			}
			table.appendOn(container);
			parent.appendChild(container);
		})
	});
	Utils.processSearch(endPoint,document.getElementById('view-witnesses-form'),"searchCase",keyword=>{
		ajax.operate({service : "viewWitnesses",id : keyword})
		.then(witnesses=>{
			let parent=document.getElementById('view-witnesses');
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Witness Name","Address","Email Address","Contact"])
			for (let witness of witnesses) {
				table.addRow([{text : witness.witnessName},{text : witness.address},{text : witness.email},{text : witness.contact}]);
			}
			table.appendOn(parent);
		})
	});
	document.getElementById('my-cases-btn').addEventListener('click', function(event){
		ajax.operate({service : "myCases"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('my-cases-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Case ID","Case Title","Case Type","Court","Client","Last Hearing", "Next Hearing",
				"Status","Outcome","Date accepted"]);
			for (let mainDetails of json) {
				table.addRow([{text : mainDetails.caseID},{text : mainDetails.caseTitle},{text : mainDetails.caseType},{text : mainDetails.courtName},
					{text : mainDetails.client},{text : mainDetails.lastHearing},{text : mainDetails.nextHearing},
					{text : mainDetails.status},{text : mainDetails.outcome},{text : mainDetails.dateAccepted}]);
			}
			table.appendOn(parent);
		})
	});
	Utils.processSearch(endPoint,document.getElementById('court-proceedings-search-form'),"searchCase",keyword=>{
		document.getElementById('court-proceedings-form').reset();
		Utils.fillForm(document.getElementById('court-proceedings-form'),["caseID"],[keyword]);
	});
	Utils.processForm(endPoint,document.getElementById('court-proceedings-form'),['caseID','hearingDate','proceedings'],'add-proceedings','addProceeding', true);
	document.getElementById('finalize-case-btn').addEventListener('click', function(event){
		ajax.operate({service : "myActiveCases"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('finalize-case-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Case ID","Case Title","Case Type","Court","Client","Last Hearing","Date accepted","Won?","Lost?","Withdrawn?"]);
			for (let mainDetails of json) {
				table.addRow([{text : mainDetails.caseID},{text : mainDetails.caseTitle},{text : mainDetails.caseType},{text : mainDetails.courtName},
					{text : mainDetails.client},{text : mainDetails.lastHearing},{text : mainDetails.dateAccepted},{
						text : "<i class='fa fa-check'></i>",
						styles : {
							color : "green",
							textAlign : "center",
							cursor : "pointer"
						},
						onclick : function(row) {
							ajax.operate({service : "finalizeCase",caseID : mainDetails.caseID,clientID : mainDetails.clientID, outcome : "WON"})
							.then(json=>{
								Utils.showMessage(json);
								if (json.status==="success") {
									row.style.display="none";
								}
							})
						}
					},{
						text : "<i class='fa fa-check'></i>",
						styles : {
							color : "red",
							textAlign : "center",
							cursor : "pointer"
						},
						onclick : function(row) {
							ajax.operate({service : "finalizeCase",caseID : mainDetails.caseID,clientID : mainDetails.clientID, outcome : "LOST"})
							.then(json=>{
								Utils.showMessage(json);
								if (json.status==="success") {
									row.style.display="none";
								}
							})
						}
					},{
						text : "<i class='fa fa-check'></i>",
						styles : {
							color : "yellow",
							textAlign : "center",
							cursor : "pointer"
						},
						onclick : function(row) {
							ajax.operate({service : "finalizeCase",caseID : mainDetails.caseID,clientID : mainDetails.clientID, outcome : "WITHDRAWN"})
							.then(json=>{
								Utils.showMessage(json);
								if (json.status==="success") {
									row.style.display="none";
								}
							})
						}
					}]);
			}
			table.appendOn(parent);
		})
	});
})();
(function payments(){
	Utils.processSearch(endPoint,document.getElementById('set-payment-form'),"searchCase",keyword=>{
		ajax.operate({service : "viewCase",id : keyword})
		.then(details=>{
			document.getElementById('set-case-payment-form').reset();
			let result=JSON.parse(details.mainDetails)[0];
			let client=JSON.parse(details.clientDetails)[0].name;
			let caseID=(result.caseID!==undefined) ? result.caseID : "This case has not been accepted yet!!!";
			let title=(result.caseTitle!==undefined) ? result.caseTitle : "This case has not been accepted yet!!!";
			Utils.fillForm(document.getElementById('set-case-payment-form'),["caseID","caseTitle","client"],[caseID,title,client]);
		})
	});
	Utils.processForm(endPoint,document.getElementById('set-case-payment-form'),['caseID','cost'],'set-case-cost','setCost', true);
	document.getElementById('request-payment-btn').addEventListener('click', function(event){
		ajax.operate({service : "requestPayCases"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('request-payment-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Case ID","Case Title","Case Type","Client","Last Hearing", "Next Hearing","Status","Total cost","Paid","Balance","Action"]);
			for (let mainDetails of json) {
				table.addRow([{text : mainDetails.caseID},{text : mainDetails.caseTitle},{text : mainDetails.caseType},
					{text : mainDetails.client},{text : mainDetails.lastHearing},{text : mainDetails.nextHearing},
					{text : mainDetails.status},{text : mainDetails.cost},{text : mainDetails.paid},{text : mainDetails.balance},{
						text : "Request",
						styles : {
							color : "green",
							cursor : "pointer",
							textAlign : "center"
						},
						onclick : function(row){
							ajax.operate({service : "requestPayment",caseID : mainDetails.caseID,clientID : mainDetails.clientID})
							.then(json=>{
								Utils.showMessage(json);
								if (json.status==="success") {
									row.style.display="none";
								}
							})
						}
					}]);
			}
			table.appendOn(parent);
		})
	});
	Utils.processSearch(endPoint,document.getElementById('case-payment-form'),"searchCase",keyword=>{
		ajax.operate({service : "viewCasePayment",id : keyword})
		.then(payments=>{
			let parent=document.getElementById('case-payment-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Payment ID","Date paid","Transaction ID","Status","Amount"])
			for (let payment of payments) {
				table.addRow([{text : payment.paymentID},{text : payment.datePayed},{text : payment.transID},{text : payment.status},{text : payment.amount}]);
			}
			table.appendOn(parent);
		})
	});
})();
(function meetings(){
	Utils.processSearch(endPoint,document.getElementById('arrange-meeting-form'),"searchCase",keyword=>{
		ajax.operate({service : "viewCase",id : keyword})
		.then(details=>{
			document.getElementById('meeting-arrange-form').reset();
			let result=JSON.parse(details.mainDetails)[0];
			let client=JSON.parse(details.clientDetails)[0].name;
			let clientID=JSON.parse(details.clientDetails)[0].clientID;
			let caseID=(result.caseID!==undefined) ? result.caseID : "This case has not been accepted yet!!!";
			let title=(result.caseTitle!==undefined) ? result.caseTitle : "This case has not been accepted yet!!!";
			Utils.fillForm(document.getElementById('meeting-arrange-form'),["clientID","caseID","caseTitle","client"],[clientID,caseID,title,client]);
		})
	});
	Utils.processForm(endPoint,document.getElementById('meeting-arrange-form'),['clientID','caseID','meetingDate','venue'],'arrange-meeting','arrangeMeeting', true);
	document.getElementById('view-arranged-btn').addEventListener('click', function(event){
		ajax.operate({service : "arrangedMeetings"})
		.then(json=>{
			let parent=document.getElementById('view-arranged-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Advocate name","Client name","Case Title","Venue","Status","Meeting Date"])
			for (let item of json) {
				table.addRow([{text : item.advocate},{text : item.client},{text : item.caseTitle},{text : item.venue},{text : item.status},
				{text: item.meetingDate}]);
			}
			table.appendOn(parent);
		})
	});
	document.getElementById('mark-done-btn').addEventListener('click', function(event){
		ajax.operate({service : "awaitingMeetings"})
		.then(json=>{
			let parent=document.getElementById('mark-done-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Advocate name","Client name","Case Title","Venue","Status","Meeting Date","Action"])
			for (let item of json) {
				table.addRow([{text : item.advocate},{text : item.client},{text : item.caseTitle},{text : item.venue},{text : item.status},
				{text: item.meetingDate},{
					text : "Done<i class='fa fa-check'></i>",
					styles : {
						color : "green",
						textAlign : "center",
						cursor : "pointer"
					},
					onclick : function(row) {
						ajax.operate({service : "markDone", id : item.id,advocateID : item.advocateID, clientID : item.clientID, caseID : item.caseID})
						.then(res=>{
							Utils.showMessage(res);
							if (res.status=='success') {
								row.style.display="none";
							}
						})
					}
				}]);
			}
			table.appendOn(parent);
		})
	});
})();
(function salary(){
	document.getElementById('my-salaries-btn').addEventListener('click', function(event){
		ajax.operate({service : "mySalaries"})
		.then(json=>{
			let parent=document.getElementById('my-salaries-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Employee ID","Name","Year","Month","Date of Payment","Amount"])
			for (let item of json) {
				table.addRow([{text : item.empID},{text : item.name},{text : item.year},{text : item.month},{text : item.datePayed},
				{text: item.salary}]);
			}
			table.appendOn(parent);
		})
	});
})();