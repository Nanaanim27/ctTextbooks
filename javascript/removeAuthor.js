function removeAuthor()
{
	var tbl = document.getElementById('inputTextbooks');
	var lastRow = tbl.rows.length;
	if (lastRow > 8) tbl.deleteRow(lastRow - 3);
}
