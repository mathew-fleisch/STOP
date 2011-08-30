<?php

$db = new mysqli('whitney', 'webUser', 'pubtr@cker!', 'annSum4');
if (mysqli_connect_errno()) {
    exit('Error connecting to db');
}
