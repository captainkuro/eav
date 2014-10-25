<?php
spl_autoload_register(function ($class) {
	require __DIR__.'/class/'.$class.'.php';
});

function hello() {
	echo 'Hello World!';
}