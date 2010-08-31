<?php
class SlimGb_InstallController extends SlimGb_Controller
{
	/**
	 * 
	 */
	protected function registerActions() {
		$this->registerIncludeAction('showInstallation', 'install');
	}

	public function showInstallation()
	{
		//TODO: installation routine
	}
}