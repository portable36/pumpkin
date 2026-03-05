<?php

namespace Infoamin\Installer\Helpers;

class PermissionsChecker
{
    /**
     * @var array
     */
    protected $datas = [];

    /**
     * Set the data array permissions and errors.
     *
     * @return mixed
     */
    public function __construct()
    {
        $this->datas['permissions'] = [];
        $this->datas['errors']      = null;
    }

    /**
     * Check for the folders permissions.
     *
     * @return array
     */
    public function checkPermission(array $folders)
    {
        foreach ($folders as $folder => $permission) {
            $path = base_path($folder);
            
            if (!file_exists($path)) {
                $this->setFile($folder, $permission, true);
                continue;
            }
            
            $currentPerm = (int) $this->getPermission($folder);
            $requiredPerm = (int) $permission;
            
            if ($currentPerm < $requiredPerm) {
                $this->setFileAndSetErrors($folder, $permission, false);
            } else {
                $this->setFile($folder, $permission, true);
            }
        }

        return $this->datas;
    }

    /**
     * Get a folder or file permission.
     *
     * @return int
     */
    private function getPermission($folder)
    {
        $path = base_path($folder);
        
        if (is_dir($path)) {
            return mb_substr(sprintf('%o', fileperms($path)), -4);
        } elseif (is_file($path)) {
            return mb_substr(sprintf('%o', fileperms($path)), -3);
        }
        
        return 0;
    }

    /**
     * Add the file to the list of results.
     */
    private function setFile($folder, $permission, $isActive)
    {
        array_push($this->datas['permissions'], [
            'folder' => $folder,
            'permission' => $permission,
            'isActive' => $isActive,
        ]);
    }

    /**
     * Add the file and set the errors.
     */
    private function setFileAndSetErrors($folder, $permission, $isActive)
    {
        $this->setFile($folder, $permission, $isActive);
        $this->datas['errors'] = true;
    }
}
