<?php
echo '<h1>OK - PHP funcionando</h1>';
echo '<p>Document Root: ' . $_SERVER['DOCUMENT_ROOT'] . '</p>';
echo '<p>Script: ' . $_SERVER['SCRIPT_FILENAME'] . '</p>';
echo '<p>CWD: ' . getcwd() . '</p>';
