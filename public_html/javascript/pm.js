function marklist(id, name, state)
{
	var parent = document.getElementById(id);
	if (!parent) {
		eval('parent = document.' + id);
	}

	if (!parent) {
		return;
	}

	var rb = parent.getElementsByTagName('input');

	for (var r = 0; r < rb.length; r++) {
		if (rb[r].name.substr(0, name.length) == name) 	{
			rb[r].checked = state;
		}
	}
}