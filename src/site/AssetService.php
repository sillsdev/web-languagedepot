<?php
namespace Site;

class AssetService
{
    public function scriptFiles($rootPath)
    {
        $it = new \RecursiveDirectoryIterator($rootPath);
        $it = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::SELF_FIRST);
        
        $scripts = array();
        foreach ($it as $file) {
            if ($file->isFile()) {
                $ext = $file->getExtension();
                $isMin = (strpos($file->getPathname(), '-min') !== false);
                if (! $isMin && $ext == 'js') {
                    $scripts[] = $file->getPathname();
                }
            }
        }
        return $scripts;
    }
}