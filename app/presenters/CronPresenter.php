<?php

namespace App\Presenters;

use Nette,
    App\Model, Nette\Application\UI\Form;


/**
 * Homepage presenter.
 */
class CronPresenter extends BasePresenter
{

    /** @var  \Models\Rss */
    private $rss;

	public function injectRss(\Models\Rss $rss)
    {
        $this->rss = $rss;
    }

	public function actionRss() {
		$this->rss->loadChannels();
		$this->terminate();
	}

}
