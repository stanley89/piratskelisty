<?php

namespace App\Presenters;

use Nette,
    App\Model;


/**
 * Lob presenter.
 */
class LobPresenter extends BasePresenter
{
    /** @var  \Models\Rss */
    private $rss;

	public function injectRss(\Models\Rss $rss)
    {
        $this->rss = $rss;
    }


    public function renderDefault($limit = 10,$offset=0)
    {
        $this->template->lobs = $this->rss->getLobs($limit, $offset);
		$this->template->next_offset = $offset + $limit;
		$this->template->limit = $limit;
		$this->payload->append = 'snippet--lobs';

        $this->redrawControl("dalsi");
        $this->redrawControl("lobs");
	}


}

