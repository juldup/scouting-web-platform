<?php

class PrivilegeController extends BaseController {
  
  public function showEdit() {
    
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    $privilegeList = Privilege::getPrivilegeArrayByCategory($this->section->id != 1);
    
    $privilegeRawData = Privilege::all();
    
    $privilegeTable = array();
    foreach ($privilegeList as $sOrU) {
      foreach ($sOrU as $category) {
        foreach ($category as $privilege) {
          $privilegeTable[$privilege['id']] = array();
          foreach ($leaders as $leader) {
            $privilegeTable[$privilege['id']][$leader->id] = "";
          }
        }
      }
    }
    
    foreach ($privilegeRawData as $privilegeData) {
      $privilegeTable[$privilegeData->operation][$privilegeData->member_id] = $privilegeData->scope;
    }
    
    return View::make('pages.leader.editPrivileges', array(
        'leaders' => $leaders,
        'privilege_list' => $privilegeList,
        'privilege_table' => $privilegeTable,
    ));
  }
    
}
