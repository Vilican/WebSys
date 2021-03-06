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
	url: window.location.href + "&mod",
	success(data){
		document.getElementById("modal").innerHTML = data;
		enableFinishButtons();
	}
  });
}

function enableModalButtons() {
	enableFinishButtons();
	if (document.getElementById("deletepost")) {
		document.getElementById("deletepost").addEventListener("click", function(){ doPost({ "act" : "delpost", "post" : document.getElementById("target").value, "csrf" : document.getElementById("csrf").value, "permanent" : 0}); }, false);
	}
	if (document.getElementById("deletepostperm")) {
		document.getElementById("deletepostperm").addEventListener("click", function(){ doPost({ "act" : "delpost", "post" : document.getElementById("target").value, "csrf" : document.getElementById("csrf").value, "permanent" : 1}); }, false);
	}
	if (document.getElementById("editpost")) {
		document.getElementById("editpost").addEventListener("click", function(){ doPost( $.param( { "act" : "editpost", "post" : document.getElementById("target").value, "csrf" : document.getElementById("csrf").value} ) + "&" + $("form").serialize() ); }, false);
	}
	if (document.getElementById("flagpost")) {
		document.getElementById("flagpost").addEventListener("click", function(){ doPost({ "act" : "flagpost", "post" : document.getElementById("target").value, "csrf" : document.getElementById("csrf").value}); }, false);
	}
}

function postPrep(action, attribute) {
  $.ajax({ type: "POST",
	data: { "post" : attribute, "act" : action },
	cache: false,
	url: window.location.href + "&mod",
	success(data){
		document.getElementById("modal").innerHTML = data;
		enableModalButtons();
		$("#modal").modal("show");
	}
  });
}

window.onload = function() {
  var i;
  var deletors = document.getElementsByClassName("post-del");
  for (i = 0; i < deletors.length; i++) {
	deletors[i].addEventListener("click", function(){ postPrep("delprep", this.getAttribute("data-postid")); }, false);
  }
  
  var editors = document.getElementsByClassName("post-edit");
  for (i = 0; i < editors.length; i++) {
	editors[i].addEventListener("click", function(){ postPrep("editprep", this.getAttribute("data-postid")); }, false);
  }
  
  var flaggers = document.getElementsByClassName("post-flag");
  for (i = 0; i < flaggers.length; i++) {
	flaggers[i].addEventListener("click", function(){ postPrep("flagprep", this.getAttribute("data-postid")); }, false);
  }
}