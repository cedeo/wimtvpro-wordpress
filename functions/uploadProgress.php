<?php
/**
 * Written by walter at 06/11/13
 */
include_once("../../../../wp-load.php");
include_once("../api/wimtv_api.php");

$contentIdentifier = $_GET['contentIdentifier'];

echo apiGetUploadProgress($contentIdentifier);