<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class FBPresenter extends BasePresenter
{



	public function renderDefault()
	{
		$this->template->feeds = array("www.facebook.com/odspraha",
		"www.facebook.com/zelenapraha",
		"www.facebook.com/TOP09.Praha",
		"www.facebook.com/KduCslPraha",
		"www.facebook.com/kscmpraha",
		"www.facebook.com/CeskaPiratskaStranaPraha",
		"www.facebook.com/ANOPraha",
		"www.facebook.com/socdempraha",
		"www.facebook.com/TomasHudecek.politik"
		
		
		);
    }




}
