import {Ajax} from "./ajax.js";
export class Utils
{
	static showMessage(data={})
	{
		let div=document.getElementsByClassName('notification')[0];
		let type=data.status==='success'? 'success' : 'error';
		if (type==='success') {
			div.classList.add('success');
			div.children[0].children[0].innerHTML='Success!';
			div.children[0].children[1].innerHTML=data.message;
		}else{
			div.classList.add('error');
			div.children[0].children[0].innerHTML='Error!';
			div.children[0].children[1].innerHTML=data.message;
		}
		div.children[1].addEventListener('click', function(event){
			div.style.display="none";
		});
		div.style.display="flex";
		div.scrollIntoView();
	}
	static processForm(action,form,items=[],submitButName,serviceName, reset=true)
	{
		let elements=form.elements;
		elements.namedItem(submitButName).addEventListener('click', function(event){
			if (form.checkValidity()) {
				let data={};
				data.service=serviceName;
				for (let item of items) {
					if (elements.namedItem(item).type==='file') {
						data[item]=elements.namedItem(item).files[0];
					}else{
						data[item]=elements.namedItem(item).value;
					}
				}
				let ajax=new Ajax(action);
				ajax.operate(data)
				.then(json=>{
					Utils.showMessage(json);
					if (json.status==="success" && reset===true) {
						form.reset();
					}
				})
			}else{
				Utils.showMessage({status : "failed", message: "Please complete the form with required information before submission."});
			}
		});
	}
	static clearPrevResults(parent,start)
	{
		if (parent.children.length>start) {
			for (let i=1;i<parent.children.length;i++) {
				parent.removeChild(parent.children[i]);
			}
		}
	}
	static processSearch(action,form,service,callback)
	{
		form.elements.namedItem('keyword').addEventListener('keyup', function(event){
			if (this.value!=="") {
				let ajax=new Ajax(action);
				ajax.operate({service, keyword : this.value})
				.then(json=>{
					if (form.children.length>2) {
						form.removeChild(form.children[2]);
					}
					let div=document.createElement("div");
					div.setAttribute("class","search-results");
					for (let item of json) {
						let set=document.createElement("div");
						set.setAttribute("data-mainKey",item.mainKey);
						let i=1;
						let keys=Object.keys(item);
						for (let i=1;i<keys.length;i++) {
							let span=document.createElement("span");
							span.innerHTML=item["key"+i];
							set.appendChild(span);
						}
						set.addEventListener('click', function(){
							form.reset();
							div.style.display="none";
							callback(item.mainKey);
						});
						div.appendChild(set);
					}
					form.appendChild(div);
				})
			}
		});
	}
	static fillForm(form,keys=[],values=[])
	{
		for (let i=0;i<keys.length;i++){
			form.elements.namedItem(keys[i]).value=values[i];
		}
	}
}