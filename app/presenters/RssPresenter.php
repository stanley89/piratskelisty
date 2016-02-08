<?php

namespace App\Presenters;

use Nette,
    App\Model;


/**
 * Homepage presenter.
 */
class RssPresenter extends BasePresenter
{

    public function renderDefault()
    {
        $this->template->clanky = $this->clanky->getClanky(10,0);

    }

    public function renderAktuality()
    {
        $this->template->clanky = $this->clanky->getClanky(10,0,"aktuality",null,true);
    }

}
