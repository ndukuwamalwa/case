export class Table
{
	constructor()
	{
		this.table=document.createElement("table");
		this.thead=document.createElement("thead");
		this.tbody=document.createElement("tbody");
	}
	header(titles=[])
	{
		let th,tr;
		tr=document.createElement("tr");
		for (let title of titles) {
			th=document.createElement("th");
			th.innerHTML=title;
			tr.appendChild(th);
		}
		this.thead.appendChild(tr);
		this.table.appendChild(this.thead);
		return this;
	}
	addRow(cols=[])
	{
		let row=document.createElement("tr");
		for (let col of cols) {
			let td=document.createElement("td");
			td.innerHTML=col.text;
			if (col.hasOwnProperty('onclick')) {
				td.addEventListener('click', function(event){
					col.onclick(td.parentElement);
				});
			}
			if (col.hasOwnProperty('styles')) {
				let keys=Object.keys(col.styles);
				for (let key of keys) {
					td.style[key]=col.styles[key];
				}
			}
			row.appendChild(td);
		}
		this.tbody.appendChild(row);
		return this;
	}
	appendOn(elem)
	{
		this.table.appendChild(this.tbody);
		elem.appendChild(this.table);
	}
}