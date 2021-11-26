<?php
$html .= '
<style type="text/css">
    .tabs .td { padding: 5px; }
</style>
<div>
    <p class="tabs">';
    if ($_GET['disktag']==''||$_GET['disktag']===true||!in_array($_GET['disktag'], $disktags)) {
        if ($_GET['settings']==='platform') $html .= '
        <span><a href="?settings">' . geti18n('Home') . '</a></span>
        <span>' . geti18n('PlatformConfig') . '</span>';
        else $html .= '
        <span>' . geti18n('Home') . '</span>
        <span><a href="?settings=platform">' . geti18n('PlatformConfig') . '</a></span>';
    } else $html .= '
        <span><a href="?settings">' . geti18n('Home') . '</a></td>
        <span><a href="?settings=platform">' . geti18n('PlatformConfig') . '</a></span>';
    foreach ($disktags as $disktag) {
        if ($disktag!='') {
            if ($_GET['disktag']===$disktag) $html .= '
        <span>' . $disktag . '</span>';
            else $html .= '
        <span><a href="?settings&disktag=' . $disktag . '">' . $disktag . '</a></span>';
        }
    }
    $html .= '
    </p>
</div><br>';