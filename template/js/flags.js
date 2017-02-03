function flag(data, post) {
	$.ajax({
		type: "POST",
		data: { 'post' : post, 'data' : data, 'csrf' : document.getElementById('csrf-posts').value },
		cache: false,
		url: "logic/flags.php",
		success: function(rt){
			location.reload();
		}
	});
	return false;
}