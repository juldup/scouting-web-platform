<?php

class LeaderController extends BaseController {
  
  public function showPage($archive = null) {
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    $countInCharge = 0;
    $countOthers = 0;
    $menInCharge = false; // Whether there is at least one male in charge
    $menInOthers = false; // Whether there is at least one male in the others
    foreach ($leaders as $leader) {
      if ($leader->leader_in_charge) {
        $countInCharge++;
        if ($leader->gender != 'F') $menInCharge = true;
      } else {
        $countOthers++;
        if ($leader->gender != 'F') $menInOthers = true;
      }
    }
    
    return View::make('pages.leader.leaders', array(
        'is_leader' => $this->user->isLeader(),
        'leaders' => $leaders,
        'count_in_charge' => $countInCharge,
        'count_others' => $countOthers,
        'men_in_charge' => $menInCharge,
        'men_in_others' => $menInOthers,
    ));
  }
  
  public function showEdit() {
    
    if (!$this->user->isLeader()) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    return View::make('pages.leader.editLeaders', array(
        'leaders' => $leaders,
    ));
  }
  
  public function showEditPrivileges() {
    
    if (!$this->user->isLeader()) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    return View::make('pages.leader.editLeaders', array(
        'leaders' => $leaders,
    ));
  }
  
  public function getLeaderPicture($leader_id) {
    $leader = Member::find($leader_id);
    if ($leader && $leader->is_leader && $leader->has_picture) {
      $path = $leader->getPicturePath();
      return Illuminate\Http\Response::create(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    }
  }
  
}
