<?php
/**
 * Written by walter at 06/11/13
 */
/**
 * Questo file viene chiamato direttamente via richiesta HTTP, in questo caso via Ajax, con parametro GET contentIdentifier.
 * Ritorna il progresso dell'upload di un file chiamando l'API corrispondente di wim.tv, in base al parametro contentIdentifier fornito.
 */
include_once("../../../../wp-load.php");
include_once("../api/wimtv_api.php");

$contentIdentifier = $_GET['contentIdentifier'];

echo apiGetUploadProgress($contentIdentifier);