<?php

namespace App\src;

class appServices
{
    public function _getHelpdezkVersion(): string
    {
        // Read the version.txt file
        $versionFile = $this->_getHelpdezkPath() . "/version.txt";

        if (is_readable($versionFile)) {
            $info = file_get_contents($versionFile, FALSE, NULL, 0, 50);
            if ($info) {
                return trim($info);
            } else {
                return '1.0';
            }
        } else {
            return '1.0';
        }

    }

    public function _getHelpdezkPath()
    {
        $path_default = $_ENV["PATH_DEFAULT"];
        if (substr($path_default, 0, 1) != '/') {
            $path_default = '/' . $path_default;
        }
        if ($path_default == "/..") {
            $path = "";
        } else {
            $path = $path_default;
        }
        
		// if in localhost document root is D:/xampp/htdocs
        $document_root = $_SERVER['DOCUMENT_ROOT'];
        if (substr($document_root, -1) != '/') {
            $document_root = $document_root . '/';
        }
        return realpath($document_root . $path);
    }

	public function _getPath()
    {
        $path_default = $_ENV["PATH_DEFAULT"];
        if (substr($path_default, 0, 1) != '/') {
            $path_default = '/' . $path_default;
        }

        if ($path_default == "/..") {
            $path = "";
        } else {
            $path = $path_default;
        }
        
		return $path;
    }

	public function _getLayoutTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/layout.latte';
    }

	public function _getNavbarTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/nav-main.latte';
    }

	public function _getFooterTemplate()
    {
        return $this->_getHelpdezkPath().'/app/modules/main/views/footer.latte';
    }

	public function _getDefaultParams(): array
    {		
		return array(
			"path"			=> $this->_getPath(),
			"lang_default"	=> $_ENV["DEFAULT_LANG"],
			"layout"		=> $this->_getLayoutTemplate(),
			"version" 		=> $this->_getHelpdezkVersion(),
			"navBar"		=> $this->_getNavbarTemplate(),
			"footer"		=> $this->_getFooterTemplate(),
			"demoVersion" 	=> empty($_ENV['DEMO']) ? 0 : $_ENV['DEMO'] // Demo version - Since January 29, 2020
		);
    }
}