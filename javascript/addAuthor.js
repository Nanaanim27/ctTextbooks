function addAuthor() {
 	var tbl = this.document.getElementById('inputTextbooks').getElementsByTagName('tbody')[0];
	var row = this.document.getElementById('authorRow');
	var cln = row.cloneNode(true);
	var i = tbl.rows.length - 4;
	cln.id = 'authorRow_' + i;
	cln.children[0].innerHTML = "Enter Textbook Author Name " + i;
	for (j = 1; j < 4; j++)
		{
			cln.children[j].children[0].value = '';
			var tempAttr = cln.children[j].children[0].name;
			cln.children[j].children[0].name = tempAttr + '_' + i;
		}
	tbl.appendChild(cln);
}
