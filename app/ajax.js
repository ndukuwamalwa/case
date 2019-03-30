'use strict';
export class Ajax
{
	constructor(endpoint)
	{
		this.url=endpoint;
	}
	operate(data={})
	{
		let cookies=document.cookie.split(";");
		for (let cookie of cookies) {
			let [key,value]=cookie.split("=");
			if (key==="AUTH-TOKEN") {
				data.token=value;
			}
		}
		return fetch(this.url,{
			method : "POST",
			body : this.formData(data),
			headers : {
				"Accept" : "application/json"
			}
		})
		.then(res=>res.text())
		.then(text=>{
			//console.log(text);
			let json=JSON.parse(text);
			if (json.status==="login") {
				window.location="http://localhost/case/login.html";
			}
			return json;
		})
		.catch(err=>{
			console.log('An error occurred.'+err);
		})
	}
	blob(data={})
	{
		return fetch(this.url,{
			method : "POST",
			body : this.formData(data),
		})
		.then(res=>res.blob())
		.then(blob=>{
			let url=URL.createObjectURL(blob);
			let img=document.createElement("img");
			img.src=url;
			return img;
		})
		.catch(err=>{
			console.log('An error occurred.'+err);
		})
	}
	formData(data={})
	{
		let formData=new FormData();
		let keys=Object.keys(data);
		for (let key of keys) {
			formData.append(key,data[key]);
		}
		return formData;
	}
}