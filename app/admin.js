'use strict';
import {Ajax} from "./ajax.js";
import {Utils} from "./utils.js";
import {Table} from "./table.js";

const endPoint="../server/services/admin.php";
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
	document.getElementById('profile-menu').addEventListener('click', function(event){
		ajax.operate({service : "viewProfile"})
		.then(json=>{
			let data=json[0];
			Utils.fillForm(document.getElementById("update-profile-form"),["fname","mname","lname","email","address","dob"],[data.fname,data.mname,data.lname,data.email,data.address,data.dobRaw]);
		});
	});
	Utils.processForm(endPoint,document.getElementById('update-profile-form'),['fname','mname','lname','email','address','dob','photo'],
		'updateProfile','updateProfile', false);
	Utils.processForm(endPoint,document.getElementById('add-contact-form'),['contact'],'add-contact','addContact', true);
	Utils.processForm(endPoint,document.getElementById('add-academic-qualification-form'),['specialization','institution','achievement','year','description'],
		'add-education','addEducation', true);
	document.getElementById('view-profile-btn').addEventListener('click', function(event){
		ajax.operate({service : "viewProfile"})
		.then(json=>{
			let data=json[0];
			let parent=document.getElementById('view-profile-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Attribute","Value"])
			.addRow([{text : "Full name"},{text : `${data.fname} ${data.lname} ${data.mname}`}])
			.addRow([{text : "Email address"},{text : data.email}])
			.addRow([{text : "Physical address"},{text : data.address}])
			.addRow([{text : "Date of Birth"},{text : data.dob}])
			.appendOn(parent);
		})
	});
	document.getElementById('view-contact-btn').addEventListener('click', function(event){
		ajax.operate({service : "viewContacts"})
		.then(json=>{
			let contacts=json;
			let parent=document.getElementById('view-contact-form').parentElement;
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
(function employee(){
	Utils.processForm(endPoint,document.getElementById('add-employee-form'),['fname','mname','lname','email','address','empType','salary','photo'],
		'add-employee','addEmployee', true);
	Utils.processSearch(endPoint,document.getElementById('employee-view-search-form'),"searchEmployee",keyword=>{
		Utils.clearPrevResults(document.getElementById('employee-view-search-form').parentElement,1);
		ajax.operate({service : "viewEmployee",id : keyword})
		.then(json=>{
			let result=json[0];
			let table=new Table();
			table.header(["Full name","Email address","Physical address","Employee type", "Monthly salary"])
			.addRow([{text : `${result.fname} ${result.lname} ${result.mname}`},{text : result.email},{text : result.address},{text :result.empType},{text :result.salary}])
			.appendOn(document.getElementById('employee-view-search-form').parentElement);
		})
	});
	Utils.processSearch(endPoint,document.getElementById('employee-delete-search-form'),"searchEmployee",keyword=>{
		let confirm=window.confirm("Are you sure you want to delete employee with ID :"+keyword);
		if (confirm) {
			ajax.operate({service : "deleteEmployee", id : keyword})
			.then(json=>{
				Utils.showMessage(json);
			})
		}
	});
	Utils.processSearch(endPoint,document.getElementById('employee-update-search-form'),"searchEmployee",keyword=>{
		ajax.operate({service : "viewEmployee",id : keyword})
		.then(json=>{
			let result=json[0];
			Utils.fillForm(document.getElementById('update-employee-form'),["empID","fname","mname","lname","email","address","empType","salary"],
				[result.empID,result.fname,result.mname,result.lname,result.email,result.address,result.empType,result.salary]);
		})
	});
	Utils.processForm(endPoint,document.getElementById('update-employee-form'),["empID",'fname','mname','lname','email','address','empType','salary'],
		'update-employee','updateEmployee', false);
	Utils.processSearch(endPoint,document.getElementById('employee-edu-search-form'),"searchEmployee",keyword=>{
		ajax.operate({service : "viewEmployee",id : keyword})
		.then(json=>{
			let result=json[0];
			Utils.fillForm(document.getElementById('emp-edu-add-form'),["empID"],[result.empID]);
		})
	});
	Utils.processForm(endPoint,document.getElementById('emp-edu-add-form'),["empID",'specialization','institution','achievement','year','description'],
		'add-education','addEmployeeEdu', true);
	Utils.processSearch(endPoint,document.getElementById('view-employee-edu-search-form'),"searchEmployee",keyword=>{
		ajax.operate({service : "viewEmployeeEdu",id : keyword})
		.then(json=>{
			let parent=document.getElementById('view-employee-edu-search-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Emp ID","Name","Specialization","Institution","Award","Year of Award","Notes"])
			for (let item of json) {
				table.addRow([{text :item.empID},{text : item.name},{text : item.specialization},{text : item.institution},{text :item.achievement},{text : item.year},{text : item.description}]);
			}
			table.appendOn(parent);
		})
	},true);
})();
(function clients(){
	document.getElementById('verify-client-btn').addEventListener('click', function(event){
		ajax.operate({service : "unVerifiedClients"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('verify-client-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Client ID","Full name","Date of Birth","Email address","Contact","Action"])
			for (let item of data) {
				table.addRow([{text : item.clientID},{text : item.name},{text : item.dob},{text : item.email},{text : item.contact},{
					text : 'Verify <i class="fa fa-check-circle"></i>',
					styles : {
						color : "green",
						textAlign : "center",
						cursor : "pointer"
					},
					onclick : function(row) {
						ajax.operate({service : "verifyClient", id : item.clientID})
						.then(json=>{
							Utils.showMessage(json);
							if (json.status==='success') {
								row.style.display="none";
							}
						})
					}
				}]);
			}
			table.appendOn(parent);
		})
	});
	document.getElementById('delete-client-btn').addEventListener('click', function(event){
		ajax.operate({service : "clients"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('delete-client-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Client ID","Full name","Date of Birth","Email address","Contact","Physical address","Verified?","Action"])
			for (let item of data) {
				table.addRow([{text : item.clientID},{text : item.name},{text : item.dob},{text : item.email},{text : item.contact},
					{text : item.address},{text : (item.verified==="1") ? "Yes" : "No"},{
					text : 'Delete <i class="fa fa-trash"></i>',
					styles : {
						color : "red",
						textAlign : "center",
						cursor : "pointer"
					},
					onclick : function(row) {
						ajax.operate({service : "deleteClient", id : item.clientID})
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
(function cases(){
	document.getElementById('applied-case-btn').addEventListener('click', function(event){
		ajax.operate({service : "appliedCases"})
		.then(json=>{
			let data=json;
			let parent=document.getElementById('applied-case-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Title","Client","Case Type","Court","Suggested advocate","First Hearing","Date applied","Description","Accept","Reject"])
			for (let item of data) {
				table.addRow([{text : item.title},{text : item.client},{text : item.caseType},{text : item.courtName},{text : item.advocate},
				{text: item.firstHearing},{text : item.dateApplied},{text : item.description},
				{
					text : "<i class='fa fa-check'></i>",
					styles : {
						color : "green",
						textAlign : "center",
						cursor : "pointer"
					},
					onclick : function(row){
						ajax.operate({service : "acceptCase", caseID : item.id,caseTitle: item.title,caseType :item.caseType,courtName : item.courtName,
							advocateID : item.advocateID,lastHearing: item.fHearing,nextHearing : item.fHearing, status : "IN PROGRESS", outcome : "LOST", clientID: item.clientID})
						.then(json=>{
							Utils.showMessage(json);
							if (json.status==='success') {
								row.style.display='none';
							}
						})
					}
				},{
					text : '<i class="fa fa-close"></i>',
					styles : {
						color : "red",
						textAlign : "center",
						cursor : "pointer"
					},
					onclick : function(row) {
						ajax.operate({service : "rejectCase", id : item.id})
						.then(json=>{
							Utils.showMessage(json);
							if (json.status==='success') {
								row.style.display='none';
							}
						})
					}
				}]);
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
	document.getElementById('view-payments-btn').addEventListener('click', function(event){
		ajax.operate({service : "viewPayments"})
		.then(json=>{
			let table=new Table();
			let parent=document.getElementById('view-payments-form').parentElement;
			Utils.clearPrevResults(parent,1);
			table.header(["Payment ID","Case ID","Date of Payment","Transaction ID","Status","Amount"])
			for (let item of json) {
				table.addRow([{text : item.paymentID},{text : item.caseID},{text : item.datePayed},{text : item.transID},{text : item.status},
				{text: item.amount}]);
			}
			table.appendOn(parent);
		})
	});
	Utils.processSearch(endPoint,document.getElementById('search-payment-form'),"searchPayments",keyword=>{
		ajax.operate({service : "viewPayment",id : keyword})
		.then(payment=>{
			let item=payment[0];
			let parent=document.getElementById('search-payment-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Payment ID","Case ID","Date of Payment","Transaction ID","Status","Amount"])
			table.addRow([{text : item.paymentID},{text : item.caseID},{text : item.datePayed},{text : item.transID},{text : item.status},
				{text: item.amount}]);
			table.appendOn(parent);
		})
	});
	document.getElementById('confirm-payment-btn').addEventListener('click', function(event){
		ajax.operate({service : "unconfirmedPayments"})
		.then(json=>{
			let parent=document.getElementById('confirm-payment-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Payment ID","Case ID","Date of Payment","Transaction ID","Status","Amount","Action"])
			for (let item of json) {
				table.addRow([{text : item.paymentID},{text : item.caseID},{text : item.datePayed},{text : item.transID},{text : item.status},
				{text: item.amount},{
					text : "Confirm<i class='fa fa-check-circle'></i>",
					styles : {
						color : "green",
						textAlign : "center",
						cursor : "pointer"
					},
					onclick : function(row){
						ajax.operate({service : "confirmPayment", id : item.paymentID})
						.then(json=>{
							Utils.showMessage(json);
							if (json.status=='success') {
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
(function salaries(){
	document.getElementById('pay-salaries-form').addEventListener('click', function(event){
		ajax.operate({service : "viewEmployees"})
		.then(json=>{
			let parent=document.getElementById('pay-salaries-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Employee ID","Full name","Email address","Profession","Monthly salary","Action"])
			for (let item of json) {
				table.addRow([{text : item.empID},{text : item.name},{text : item.email},{text : item.empType},{text : item.salary},{
					text : "Pay<i class='fa fa-check'></i>",
					styles : {
						color : "green",
						cursor : "pointer",
						textAlign : "center"
					},
					onclick : function(row) {
						ajax.operate({service : "paySalary", id : item.empID})
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
	document.getElementById('view-prev-payments-btn').addEventListener('click', function(event){
		ajax.operate({service : "paymentHistory"})
		.then(json=>{
			let parent=document.getElementById('view-prev-payments-form').parentElement;
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
(function meetings(){
	document.getElementById('view-arranged-meetings-btn').addEventListener('click', function(event){
		ajax.operate({service : "arrangedMeetings"})
		.then(json=>{
			let parent=document.getElementById('view-arranged-meetings-form').parentElement;
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
	document.getElementById('cancel-arrangement-btn').addEventListener('click', function(event){
		ajax.operate({service : "cancellableMeetings"})
		.then(json=>{
			let parent=document.getElementById('cancel-arrangement-form').parentElement;
			Utils.clearPrevResults(parent,1);
			let table=new Table();
			table.header(["Advocate name","Client name","Case Title","Venue","Status","Meeting Date","Action"])
			for (let item of json) {
				table.addRow([{text : item.advocate},{text : item.client},{text : item.caseTitle},{text : item.venue},{text : item.status},
				{text: item.meetingDate},{
					text : "Cancel<i class='fa fa-close'></i>",
					styles : {
						color : "red",
						textAlign : "center",
						cursor : "pointer"
					},
					onclick : function(row) {
						ajax.operate({service : "cancelArrangement", id : item.id,advocateID : item.advocateID, clientID : item.clientID, caseID : item.caseID})
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