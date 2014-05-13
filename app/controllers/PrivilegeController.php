<?php

class PrivilegeController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  public function showEdit() {
    
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    
    $canEditPrivileges = $this->user->can(Privilege::$EDIT_LEADER_PRIVILEGES, $this->section);
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    // List of privileges
    $privilegeList = Privilege::getPrivilegeArrayByCategory($this->section->id != 1);
    // List of active privileges
    $privilegeRawData = Privilege::all();
    
    $privilegeTable = array();
    foreach ($privilegeList as $category) {
      foreach ($category as $privilegeData) {
        $privilege = $privilegeData['privilege'];
        $sOrU = $privilegeData['scope'];
        $privilegeTable[$privilege['id']] = array();
        foreach ($leaders as $leader) {
          $privilegeTable[$privilege['id']][$leader->id] = array(
              'S' => array(
                  'state' => false,
                  'can_change' => $canEditPrivileges && $this->user->can($privilege, $this->section) && !$this->user->isOwnerOfMember($leader->id),
              ),
              'U' => array(
                  'state' => false,
                  'can_change' => $canEditPrivileges && $this->user->can($privilege, 1) && !$this->user->isOwnerOfMember($leader->id),
              ),
          );
        }
      }
    }
    
    // Set state to 'true' for all active privileges
    foreach ($privilegeRawData as $privilegeData) {
      if (isset($privilegeTable[$privilegeData->operation][$privilegeData->member_id])) {
        $privilegeTable[$privilegeData->operation][$privilegeData->member_id][$privilegeData->scope]['state'] = true;
      }
    }
    
    return View::make('pages.leader.editPrivileges', array(
        'leaders' => $leaders,
        'privilege_list' => $privilegeList,
        'privilege_table' => $privilegeTable,
    ));
  }
  
  public function updatePrivileges() {
    $privileges = Input::all();
    $error = false;
    foreach ($privileges as $privilegeData=>$state) {
      try {
        $privilegeData = explode(":", $privilegeData);
        $operation = str_replace("_", " ", $privilegeData[0]);
        $scope = $privilegeData[1];
        $leaderId = $privilegeData[2];
        $leader = Member::find($leaderId);
        // A leader cannot grant privileges to themselves
        if ($this->user->isOwnerOfMember($leaderId)) {
          $error = true;
          continue;
        }
        // A leader can only grant privileges that they have
        if ($this->user->can(Privilege::$EDIT_LEADER_PRIVILEGES, $leader->section_id)
                && $this->user->can($operation, $leader->section_id)) {
          $state = $state == "true" ? true : false;
            Privilege::set($operation, $scope, $leaderId, $state);
        } else {
          $error = true;
        }
      } catch (Exception $e) {
        $error = true;
      }
    }
    
    if ($error) return json_encode(array("result" => "Failure"));
    else return json_encode(array("result" => "Success"));
  }
    
}
