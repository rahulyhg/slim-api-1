<?php
namespace SlimApi\Skeleton;

class SkeletonService implements SkeletonInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($structure)
    {
        $this->structure = $structure;
    }

    /**
     * {@inheritdoc}
     */
    public function create($path, $name, $structure = false)
    {
        if (false === $structure) {
            $structure = $this->structure;
        }

        // should only happen the first time
        if ( ! is_dir($path)) {
            mkdir($path, 0777, true);
        }

    	foreach ($structure as $folder => $subFolder) {
    		// Folder with subfolders
    		if (is_array($subFolder)) {
    			$newPath = "{$path}/{$folder}";
    			if ( ! is_dir($newPath)) {
                    mkdir($newPath);
                }
    			$this->create($newPath, $name, $subFolder);
    		} else {
                // filename with content
                $content  = strtr($subFolder, ['$name' => $name]);
    			$filePath = "{$path}/{$folder}";
    			if ( ! is_file($filePath)) {
                    file_put_contents($filePath, $content);
                }
    		}
    	}
    }
}
