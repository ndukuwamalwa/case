'use strict';
import {Ajax} from "./ajax.js";
import {Utils} from "./utils.js";
import {Table} from "./table.js";
//I did not write the following code involving fetch.
// Store a copy of the fetch function
let _oldFetch = fetch; 

// Create our new version of the fetch function
window.fetch = function(){

    // Create hooks
    let fetchStart = new Event( 'fetchStart', { 'view': document, 'bubbles': true, 'cancelable': false } );
    let fetchEnd = new Event( 'fetchEnd', { 'view': document, 'bubbles': true, 'cancelable': false } );
    
    // Pass the supplied arguments to the real fetch function
    let fetchCall = _oldFetch.apply(this, arguments);
    
    // Trigger the fetchStart event
    document.dispatchEvent(fetchStart);
    
    fetchCall.then(function(){
        // Trigger the fetchEnd event
        document.dispatchEvent(fetchEnd);
    }).catch(function(){
        // Trigger the fetchEnd event
        document.dispatchEvent(fetchEnd);
    });
    
    return fetchCall;
};
let loader=document.getElementsByClassName('loader');
document.addEventListener('fetchStart', function() {
    if (loader.length>0) {
        loader[0].style.display="block";
    }
});
document.addEventListener('fetchEnd', function() {
     if (loader.length>0) {
        loader[0].style.display="none";
    }
});
(function router(){
    let menuList=document.getElementsByClassName('menu-list');
    if (menuList.length===0) {
        return;
    }
    let container=document.getElementById('content');
    if (container===null) {
        return;
    }
    for (let menuItem of menuList) {
        for (let listItem of menuItem.children) {
            let anchor=listItem.children[0];
            anchor.addEventListener('click', function(event){
                for (let subDivs of container.children) {
                    subDivs.style.display="none";
                }
                if (document.querySelector(anchor.hash)!==null) {
                    document.querySelector(anchor.hash).style.display="block";
                }
            });
        }
    }
    if (window.location.hash!=="") {
        for (let subDivs of container.children) {
            subDivs.style.display="none";
        }
        if (document.querySelector(window.location.hash)!==null) {
            document.querySelector(window.location.hash).style.display="block";
        }
    }else{
        document.querySelector("#dashboard").style.display="block";
    }
})();
(function init(){
    for (let form of document.forms) {
        form.onsubmit=()=>false;
    }
})();
/*(function logout(){
    let but=document.getElementById('logout');
    if (but!==null) {
        but.addEventListener('click', function(event){
            let ajax=new Ajax("server/services/auth.php");
            ajax.operate({service : "logout"})
            .then(res=>{
                 document.cookie='AUTH-TOKEN="";';
                 window.location="";
            });
        });
    }
})();*/
let statsDiv=document.getElementById('sys-stats');
if (statsDiv!==null) {
    window.addEventListener("load", function(event){
        let ajax=new Ajax("server/services/general.php");
        ajax.operate({service : 'systemStats'})
        .then(stats=>{
            for (let key of Object.keys(stats)) {
                if (key==="caseTypes") {
                    continue;
                }
                document.getElementById('stats-'+key).innerHTML=stats[key];
            }
            let table=new Table();
            table.header(["","#","Type of case", "Number handled","Average cost per case"]);
            let caseTypes=stats.caseTypes;
            for (let key of Object.keys(caseTypes)) {
                let index=Number.parseInt(key)+1;
                table.addRow([{text : "<i class='fa fa-check-circle'></i>", styles : {
                    color : "green",
                    textAlign : "center"
                }},{text : index},{text : caseTypes[key]['caseType'], styles : {
                    textAlign : "left"
                }},{text : caseTypes[key]['total']},{
                    text : `KES. ${caseTypes[key]['average']}`
                }]);
            }
            table.appendOn(document.getElementById('case-types'));
        });
    });
}
(function login(){
    let form=document.getElementById('login-form');
    if (form!==null) {
        form.onsubmit=()=>false;
        form.elements.namedItem('login').addEventListener('click', function(event){
            if (form.checkValidity()) {
                let ajax=new Ajax("server/services/auth.php");
                let elements=form.elements;
                ajax.operate({service : "login", username : elements.namedItem('username').value, password : elements.namedItem('password').value})
                .then(res=>{
                    if (res.status==="failed") {
                        Utils.showMessage(res);
                    }else{
                        document.cookie='AUTH-TOKEN='+res.token+";";
                        if (res.message==="ADMIN") {
                            window.location="admin";
                        }else if (res.message==="ADVOCATE") {
                            window.location="advocate";
                        }else if (res.message==="CLIENT") {
                            window.location="client";
                        }else{
                            Utils.showMessage({status : "failed", message : "A technical error ocurred. Please contact administrator."});
                        }
                    }
                });
            }else{
                Utils.showMessage({status : "failed", message : "Please complete the required fileds"});
            }
        });
    }
})();
(function register(){
    let form=document.getElementById('register-form');
    if (form!==null) {
        let elements=form.elements;
        elements.namedItem('addclient').addEventListener('click', function(event){
            if (form.checkValidity()) {
                if (elements.namedItem("password").value===elements.namedItem('confirm').value) {
                    let ajax=new Ajax("server/services/general.php");
                    ajax.operate({service : "register",fname : elements.namedItem("fname").value,lname : elements.namedItem('lname').value,
                    mname : elements.namedItem("mname").value, dob : elements.namedItem('dob').value, contact : elements.namedItem('contact').value,
                    email : elements.namedItem('email').value, address : elements.namedItem('address').value, password : elements.namedItem('password').value})
                    .then(res=>{
                        if (res.status==="success") {
                            ajax=new Ajax("server/services/auth.php");
                            ajax.operate({service : "login", username : elements.namedItem('email').value, password : elements.namedItem('password').value})
                            .then(res=>{
                                if (res.status==="failed") {
                                    Utils.showMessage(res);
                                }else{
                                    document.cookie='AUTH-TOKEN='+res.token+";";
                                    window.location="client";
                                }
                            })
                        }else{
                            Utils.showMessage({status : "failed", message : "An error ocurred. Try again."});
                        }
                    });
                }else {
                    Utils.showMessage({status : "failed", message : "Your passwords do not match."});
                }
            }else {
                Utils.showMessage({status : "failed", message : "Please complete the required fileds"});
            }
        });
    }
})();