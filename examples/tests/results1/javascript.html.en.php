<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src='somefile/<?php echo htmlspecialchars($t->js_file);?>'></script>



</head>

<script language="javascript">

// some sample javascript that might cause problemss

function CheckDuplicates (AddListContainer, RemoveListContainer) { 
    var AddList = eval('document.main_form.'+AddListContainer); 
    var RemoveList = eval('document.main_form.'+RemoveListContainer); 
    var TempAddList = AddList.value; 
    var TempRemoveList = RemoveList.value; 
    if (TempAddList>''&&TempRemoveList>'') { 
        TempAddList = TempAddList.substring(0,TempAddList.length-1); 
    }
}

//<!-- 

function CheckDuplicates2 (AddListContainer, RemoveListContainer) { 
    var AddList = eval('document.main_form.'+AddListContainer); 
    var RemoveList = eval('document.main_form.'+RemoveListContainer); 
    var TempAddList = AddList.value; 
    var TempRemoveList = RemoveList.value; 
    if (TempAddList>''&&TempRemoveList>'') { 
        TempAddList = TempAddList.substring(0,TempAddList.length-1); 
    }
}


-->
 
</script>

<!--

// and now just commented out stuff.. that may cause problems

function CheckDuplicates (AddListContainer, RemoveListContainer) { 
    var AddList = eval('document.main_form.'+AddListContainer); 
    var RemoveList = eval('document.main_form.'+RemoveListContainer); 
    var TempAddList = AddList.value; 
    var TempRemoveList = RemoveList.value; 
    if (TempAddList>''&&TempRemoveList>'') { 
        TempAddList = TempAddList.substring(0,TempAddList.length-1); 
    }
}
 
-->
<body> 


</body>
</html>