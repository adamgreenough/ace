<?php
// Load plugins from the /plugins/ folder
function load_plugins() {
    $pluginDirs = array_filter(glob('plugins/*'), 'is_dir');
    foreach ($pluginDirs as $pluginDir) {
        $pluginName = basename($pluginDir);
        $pluginFile = 'plugins/' . $pluginName . '/' . $pluginName . '.php';
        if (file_exists($pluginFile)) {
            require $pluginFile;
        }
    }
}
