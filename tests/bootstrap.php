<?php

spl_autoload_register(function ($className) {
	if (is_file($filePath = 'src/' . str_replace('\\', '/', $className) . '.php')) {
		require $filePath;
	}
});