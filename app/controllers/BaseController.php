<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
  
  protected function preExecute() {
    View::share('user', Member::disconnectedMember());
  }
  
  protected function checkAccessToGestion() {
    return true; // TODO Check if user is an animator
  }

}