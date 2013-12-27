<?php

class ContactController extends BaseController {
  
  public function showPage() {
    return View::make('pages.contacts', array(
        "unitLeaders" => array(Member::first(),Member::first(),Member::first(),Member::first(),Member::first(),),
        "sectionLeaders" => array(Member::first(),Member::first(),Member::first(),Member::first(),Member::first(),),
    ));
  }
  
}
