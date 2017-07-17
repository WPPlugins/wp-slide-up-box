function isInt(n) {
   return n % 1 == 0;
}

function supValidateForm() {
var x=document.forms["sup_addnew"]["sup_imgid"].value;
if (x==null || x=="")
  {
  alert("Image ID not set.");
  return false;
  }
else if (!isInt(x)) {
  alert("Image ID must be an integer number.");
  return false;
 }
}

jQuery(document).ready(function($){
	$('.errtext').click(function(){
		$(this).fadeOut(200);
	});
});