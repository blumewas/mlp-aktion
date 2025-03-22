<?php

/**
 * Helper functions for our plugin.
 *
 * @link              https://bonnermedis.de
 * @since             {VERSION}
 * @package           Mlp_Aktion
 */
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (! function_exists('slugify')) {

    /**
     * Slugify a string
     *
     * @param string $string - string to slugify
     * @param string $slugChar - character to used to create the slug {default: '-'}
     * @return string - the slugified string
     */
    function slugify(string $string, string $slugChar = '-'): string
    {
        // Convert PascalCase or camelCase to hyphenated lowercase
        $string = preg_replace('/([a-z0-9])([A-Z])/', "\$1$slugChar\$2", $string);

        // Convert to lowercase
        $string = strtolower($string);

        // Replace non-alphanumeric characters with hyphens
        $string = preg_replace('/[^a-z0-9-]/', $slugChar, $string);

        // Replace multiple hyphens with a single one
        $string = preg_replace('/-+/', $slugChar, $string);

        // Trim leading/trailing hyphens
        $string = trim($string, $slugChar);

        return $string;
    }
}

if (! function_exists('class_basename')) {
    /**
     * Get the class base name
     *
     * @param object|string $classStringOrObject
     * @return string
     */
    function class_basename($classStringOrObject): string
    {
        // If we got an object get class name
        if (! is_string($classStringOrObject)) {
            $classStringOrObject = get_class($classStringOrObject);
        }

        return basename($classStringOrObject);
    }
}

if (! function_exists('admin_asset')) {
    /**
     * Get admin asset path
     *
     * @param string $path
     * @return string - the relative path
     */
    function admin_asset(string $path): string
    {
        // prepend assets/admin path
        return project_path("assets/admin/$path");
    }
}

if (! function_exists('project_path')) {
    /**
     * Get relative path to our project.
     *
     * @param string $path
     * @return string - the relative path
     */
    function project_path(string $path): string
    {
        // Relative
        $project_root = project_root();

        return sanitize_path($path, $project_root);
    }
}

if (! function_exists('project_root')) {
    /**
     * Get the project root
     *
     * @return string
     */
    function project_root(): string
    {
        $dir = __DIR__;

        // Iterate through directories up
        while (! file_exists($dir . '/composer.json') && ! file_exists($dir . '/wp-content/plugins')) {
            $dir = dirname($dir);
        }

        // If it's a Composer project, return that root
        if (file_exists($dir . '/composer.json')) {
            return $dir;
        }

        // If it's a WordPress plugin, return the plugin's root directory
        if (file_exists($dir . '/wp-content/plugins')) {
            return plugin_dir_path(__FILE__); // For WordPress plugin root
        }

        // Fallback to current directory if not found
        return $dir;
    }
}

if (! function_exists('sanitize_path')) {
    /**
     * Sanitize the path to prevent path traversal
     *
     * @param string $path
     * @param ?string $base_dir
     * @return false|string - false if not existing
     */
    function sanitize_path($path, $base_dir = null) {
        // Ensure the base directory is absolute and resolve the path
        $base_dir = $base_dir ? realpath($base_dir) : realpath(project_root());
        $safe_path = realpath($base_dir . DIRECTORY_SEPARATOR . $path);

        // Check if the path is inside the base directory
        if (strpos($safe_path, $base_dir) !== 0 || !file_exists($safe_path)) {
            return false; // Path traversal or file doesn't exist
        }

        return $safe_path; // Safe path for use
    }
}
