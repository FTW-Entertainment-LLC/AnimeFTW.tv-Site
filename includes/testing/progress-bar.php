<?php

function progressBar($percentage) {
    $data = "<div id=\"progress-bar\" class=\"all-rounded\">\n<div id=\"progress-bar-percentage\" class=\"all-rounded\" style=\"width: $percentage%\">";
        if ($percentage > 5) { $data .= "$percentage%";} else {$data .= "<div class=\"spacer\"> </div>";}
    $data .= "</div></div>";
    return $data;
}

?>
<html>
<head>
<style type="text/css">
.all-rounded {
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
}

.spacer {
    display: block;
}

#progress-bar {
    width: 300px;
    margin: 0 auto;
    background: #cccccc;
    border: 3px solid #f2f2f2;
}

#progress-bar-percentage {
    background: #3063A5;
    padding: 5px 0px;
    color: #FFF;
    font-weight: bold;
    text-align: center;
}
</style>
</head>
<body>
<?=progressBar(60);?>
</body>
</html>