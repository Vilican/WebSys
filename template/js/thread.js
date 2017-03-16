function hideModal(reload) {
  $("#modal").modal("hide");
  document.getElementById("modal").innerHTML = null;
  if (reload) {
	location.reload();
  }
}

function enableFinishButtons() {
	if (document.getElementById("stop")) {
		document.getElementById("stop").addEventListener("click", function(){ hideModal(false); }, false);
	}
	if (document.getElementById("stopreload")) {
		document.getElementById("stopreload").addEventListener("click", function(){ hideModal(true); }, false);
	}
}

function doPost(data) {
  $.ajax({ type: "POST",
	data,
	cache: false,
	url: window.location.href + "&modth",
	success(data){
		document.getElementById("modal").innerHTML = data;
		enableFinishButtons();
	}
  });
}

function enableModalButtons() {
	enableFinishButtons();
	if (document.getElementById("deletethread")) {
		document.getElementById("deletethread").addEventListener("click", function(){ doPost({ "act" : "delthread", "thread" : document.getElementById("target").value, "csrf" : document.getElementById("csrf").value}); }, false);
	}
	if (document.getElementById("editthread")) {
		document.getElementById("editthread").addEventListener("click", function(){ doPost( $.param( { "act" : "editthread", "thread" : document.getElementById("target").value, "csrf" : document.getElementById("csrf").value} ) + "&" + $("form").serialize() ); }, false);
	}
	if (document.getElementById("addthread")) {
		document.getElementById("addthread").addEventListener("click", function(){ doPost( $.param( { "act" : "addthread", "csrf" : document.getElementById("csrf").value} ) + "&" + $("form").serialize() ); }, false);
	}

}

function postPrep(action, attribute) {
  $.ajax({ type: "POST",
	data: { "thread" : attribute, "act" : action },
	cache: false,
	url: window.location.href + "&modth",
	success(data){
		document.getElementById("modal").innerHTML = data;
		enableModalButtons();
		$("#modal").modal("show");
	}
  });
}

window.onload = function() {
  var i;
  var deletors = document.getElementsByClassName("thread-del");
  for (i = 0; i < deletors.length; i++) {
	deletors[i].addEventListener("click", function(event){ event.preventDefault(); postPrep("delprep", this.getAttribute("data-postid")); }, false);
  }
  
  var editors = document.getElementsByClassName("thread-edit");
  for (i = 0; i < editors.length; i++) {
	editors[i].addEventListener("click", function(event){ event.preventDefault(); postPrep("editprep", this.getAttribute("data-postid")); }, false);
  }
  
  var adders = document.getElementsByClassName("thread-add");
  for (i = 0; i < adders.length; i++) {
	adders[i].addEventListener("click", function(event){ event.preventDefault(); postPrep("addprep", null); }, false);
  }
}