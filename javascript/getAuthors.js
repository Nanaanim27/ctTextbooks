function getAuthors() {
	var tbl = this.document.getElementById('inputTextbooks').getElementsByTagName('tbody')[0];
  	var $rows = $(":tr[id^='authorRow']");
  	var ele = $rows.get();

  	return ele;
}
