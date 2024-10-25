<!doctype HTML>
<html>
<head>
	<title><?= $title; ?></title>
	<meta charset="UTF-8">
	<meta name="description" content="<?= $description; ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta property="og:title" content="<?= $title; ?>">
	<meta property="og:description" content="<?= $description; ?>">
	<meta property="og:type" content="website">
	<?php
		if (!empty($image)) {
			printf('<meta property="og:image" content="%s">', $image);
		}
	?>

	<link rel="stylesheet" href="<?= get_theme_directory_url(); ?>/assets/normalize.min.css" type="text/css">	
	<link rel="stylesheet" href="<?= get_theme_directory_url(); ?>/assets/styles.css" type="text/css">
</head>
<body>
	<header class="site-header">
		<a href="<?= $config['base_path'] ?>/">
			<div class="logo"><?= substr($config['blog_name'], 0, 1) ?></div> <?= $config['blog_name'] ?>
		</a>
	</header>
	
	<main class="container">