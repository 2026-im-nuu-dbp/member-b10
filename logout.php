<?php

require_once 'member_helper.php';

logout_member();

header('Location: index.php');
exit;
