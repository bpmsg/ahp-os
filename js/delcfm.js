/* confirm before delete */
function deletconfig(){ 
	var del=confirm("Are you sure to delete?");
	if (del){ 
	} 
	else { 
		alert("Record not deleted")
	}
	return del;
} 
