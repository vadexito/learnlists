<?php 

namespace LrnlUser\View\Helper;

use Zend\View\Helper\AbstractHelper;

class HasRole extends AbstractHelper
{
    public function __invoke($role = NULL)
    {
        if (!$this->getView()->zfcUserIdentity()){
            return $role === NULL ? 'guest' : ($role === 'guest');
        }
        $user = $this->getView()->zfcUserIdentity();
        if (!count($user->getRoles())){
            throw new \Exception('The user '.$user->getUserName().'should have a role defined.');
        }
        if ($role === NULL){
            return $user->getRoles()[0]->getRoleId();
        }
        
        $hasRole = false;
        foreach ($user->getRoles() as $role){
            if ($role->getRoleId() == $role){
                $hasRole = true;
                continue;
            }
        }
        return $hasRole;
    }
}