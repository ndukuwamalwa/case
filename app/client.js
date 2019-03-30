'use strict';
import {Ajax} from "./ajax.js";
import {Utils} from "./utils.js";
import {Table} from "./table.js";

const endPoint="../server/services/client.php";
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
window.onload=function(){
	let hash=window.location.hash
	if (hash==="" || hash==="#dashboard") {
		dashboard();
	}else if (hash==="#notifications") {
		notifications();
	}else{}
};
(function checkNotification(){
	setInterval(function(){
		ajax.operate({service : "newNotifications"})
		.then(json=>{
			document.getElementById("noficationCount").innerHTML=json[0].total;
		})
	},600000);
})();
window.addEventListener("load",function(event){
	setTimeout(function(){
		ajax.operate({service : "newNotifications"})
		.then(json=>{
			document.getElementById("noficationCount").innerHTML=json[0].total;
		})
	},1000);
});
function notifications()
{
	ajax.operate({service : "notifications"})
	.then(notifications=>{
		let parent=document.getElementById('notifications');
		if (parent.children.length>1) {
			for (let child of parent.children) {
				child.style.display="none";
			}
		}
		for (let notification of notifications) {
			let div=document.createElement("div");
			div.setAttribute("class","notification-item");
			let span=document.createElement("span");
			span.setAttribute("title","Click o view");
			span.innerHTML=`<i class='fa fa-chevron-down'></i> ${notification.caseTitle} -(${notification.dateAdded})`;
			div.appendChild(span);
			let message=document.createElement("p");
			message.innerHTML=notification.message;
			div.appendChild(message);
			span.addEventListener("click", function(event){
				if (message.style.display==="block") {
					span.children[0].className="fa fa-chevron-down";
					message.style.display="none";
				}else{
					span.children[0].className="fa fa-chevron-up";
					message.style.display="block";
				}
				if (notification.seen==="0") {
					ajax.operate({service : "markNotification", id : notification.id});
					setTimeout(function(){
						ajax.operate({service : "newNotifications"})
						.then(json=>{
							document.getElementById("noficationCount").innerHTML=json[0].total;
						})
					},100);
				}
			});
			parent.appendChild(div);
		}
	})
}
document.getElementById("dash").addEventListener('click', function(event){
	dashboard();
});
(function profile(){
	document.getElementById('view-profile-btn').addEventListener('click', function(event){
		ajax.operate({service : "viewProfile"})
		.then(json=>{
			let data=json[0];
			let parent=document.getElementById('view-profile-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Attribute","Value"])
			.addRow([{text : "Client ID"},{text : data.clientID}])
			.addRow([{text : "Full name"},{text : data.name}])
			.addRow([{text : "Date of Birth"},{text : data.dob}])
			.addRow([{text : "Email address"},{text : data.email}])
			.addRow([{text : "Physical address"},{text : data.address}])
			.addRow([{text : "Contact"},{text : data.contact}])
			.appendOn(parent);
		})
	});
	document.getElementById('new-notifications').addEventListener('click', function(event){
		notifications();
	});
})();
(function cases(){
	Utils.processSearch(endPoint,document.getElementById('apply-case-search-form'),"searchAdvocate",keyword=>{
		document.getElementById('apply-case-form').reset();
		Utils.fillForm(document.getElementById('apply-case-form'),["advocateID"],[keyword]);
	});
	Utils.processForm(endPoint,document.getElementById('apply-case-form'),['advocateID','caseTitle','caseType','courtName','firstHearing','description'],'apply-case','addCase', true);
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
				table.addRow([{text : proceeding.hearingDate},{text : proceeding.proceedings}]);
			}
			table.appendOn(container);
			parent.appendChild(container);
		})
	});
	document.getElementById('cancel-case-btn').addEventListener('click', function(event){
		ajax.operate({service : "appliedCases"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('cancel-case-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Title","Client","Case Type","Court","Suggested advocate","First Hearing","Date applied","Action"])
			for (let item of data) {
				table.addRow([{text : item.title},{text : item.client},{text : item.caseType},{text : item.courtName},{text : item.advocate},
				{text: item.firstHearing},{text : item.dateApplied},{
					text : "<i class='fa fa-close'></i>Cancel",
					styles : {
						textAlign : "center",
						color : "red",
						cursor : "pointer"
					},
					onclick : function (row){
						ajax.operate({service : "cancelCase",caseID : item.id})
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
	Utils.processSearch(endPoint,document.getElementById('add-witnesses-search-form'),"searchCase",keyword=>{
		ajax.operate({service : "viewCase",id : keyword})
		.then(details=>{
			document.getElementById('add-witnesses-form').reset();
			let result=JSON.parse(details.mainDetails)[0];
			let caseID=(result.caseID!==undefined) ? result.caseID : "This case has not been accepted yet!!!";
			let title=(result.caseTitle!==undefined) ? result.caseTitle : "This case has not been accepted yet!!!";
			Utils.fillForm(document.getElementById('add-witnesses-form'),["caseID","caseTitle"],[caseID,title]);
		})
	});
	Utils.processForm(endPoint,document.getElementById('add-witnesses-form'),['caseID','witnessName','address','contact','email'],'add-witnesses','addWitness', true);
	Utils.processSearch(endPoint,document.getElementById('view-witnesses-form'),"searchCase",keyword=>{
		Utils.clearPrevResults(document.getElementById('view-witnesses-form').parentElement,1);
		ajax.operate({service : "witnesses",caseID : keyword})
		.then(witnesses=>{
			let table=new Table();
			table.header(["Case Title","Witness Name","Contact","Email address", "Address","Action"]);
			for (let witness of witnesses) {
				table.addRow([{text : witness.caseTitle},{text : witness.witnessName},{text : witness.contact},{text : witness.email},{text : witness.address},{
					text : "<i class='fa fa-trash'></i>",
					styles : {
						color : "red",
						textAlign : "center",
						cursor : "pointer"
					},
					onclick : function(row){
						ajax.operate({service : "deleteWitness", id : witness.id})
						.then(json=>{
							Utils.showMessage(json);
							if (json.status==="success") {
								row.style.display="none";
							}
						})
					}
				}])
			}
			table.appendOn(document.getElementById('view-witnesses-form').parentElement);
		})
	});
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
	Utils.processSearch(endPoint,document.getElementById('view-proceedings-form'),"searchCase",keyword=>{
		Utils.clearPrevResults(document.getElementById('view-proceedings-form').parentElement,1);
		ajax.operate({service : "proceedings",caseID : keyword})
		.then(proceedings=>{
			let table=new Table();
			table.header(["Hearing Date","Proceedings"]);
			for (let proceeding of proceedings) {
				table.addRow([{text : proceeding.hearingDay},{text : proceeding.proceedings}])
			}
			table.appendOn(document.getElementById('view-proceedings-form').parentElement);
		})
	});
})();
(function payments(){
	document.getElementById('view-costs-btn').addEventListener('click', function(event){
		ajax.operate({service : "viewCosts"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('view-costs-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Case ID","Case Title","Case Type","Client","Last Hearing", "Next Hearing","Status","Total cost","Paid","Balance"]);
			for (let mainDetails of json) {
				table.addRow([{text : mainDetails.caseID},{text : mainDetails.caseTitle},{text : mainDetails.caseType},
					{text : mainDetails.client},{text : mainDetails.lastHearing},{text : mainDetails.nextHearing},
					{text : mainDetails.status},{text : mainDetails.cost},{text : mainDetails.paid},{text : mainDetails.balance}]);
			}
			table.appendOn(parent);
		})
	});
	Utils.processSearch(endPoint,document.getElementById('pay-search-form'),"searchCase",keyword=>{
		ajax.operate({service : "viewCase",id : keyword})
		.then(details=>{
			document.getElementById('pay-form').reset();
			let result=JSON.parse(details.mainDetails)[0];
			let caseID=(result.caseID!==undefined) ? result.caseID : "This case has not been accepted yet!!!";
			let title=(result.caseTitle!==undefined) ? result.caseTitle : "This case has not been accepted yet!!!";
			Utils.fillForm(document.getElementById('pay-form'),["caseID","caseTitle"],[caseID,title]);
		})
	});
	Utils.processForm(endPoint,document.getElementById('pay-form'),['caseID','transID','amount'],'add-pay','pay', true);
	Utils.processSearch(endPoint,document.getElementById('case-payment-form'),"searchCase",keyword=>{
		ajax.operate({service : "casePayments",caseID : keyword})
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
	document.getElementById('arranged-meetings-btn').addEventListener('click', function(event){
		ajax.operate({service : "meetings"})
		.then(json=>{
			let parent=document.getElementById('arranged-meetings-form').parentElement;
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
})();