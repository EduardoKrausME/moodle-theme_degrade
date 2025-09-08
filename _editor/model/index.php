<?php

require_once("../../../../config.php");
require_once("../editor-lib.php");

header("Content-Type: application/json");
echo json_encode(list_templates());
