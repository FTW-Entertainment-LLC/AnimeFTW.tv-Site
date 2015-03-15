<?php
if($_SERVER['PHP_SELF'] != '/error.php'){
	
include('includes/classes/footer.class.php');

$footer = new AFTWFooter();
$footer->Output();

if($profileArray[2] == 1){
	echo '<script type="text/javascript" src="/scripts/aftw.js?v=001"></script>';
}
else {
	echo '<script type="text/javascript" src="/scripts/aftw.min.js?v=001"></script>';
}
if($_SERVER['PHP_SELF'] == '/videos.php'){
	echo '<div id="shadow" style="display:none;">0</div>';
}
?>
</body>
<?
if($_SERVER['PHP_SELF'] == '/forums.php' && ($profileArray[2] != 0 || $profileArray[2] != 3)){
	echo '<script language="javascript">
$(function(){
 
    // add multiple select / deselect functionality
    $("#selectall").click(function () {
          $(\'.modcheck\').attr(\'checked\', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    $(".modcheck").click(function(){
 
        if($(".modcheck").length == $(".modcheck:checked").length) {
            $("#selectall").attr("checked", "checked");
        } else {
            $("#selectall").removeAttr("checked");
        }
 
    });
});
</script>';
}
}
?>
</html>