<?php

namespace App\RedakceModule\Presenters;

use Nette,
    App\Model;
use Nette\Application\UI\Form;

/**
 * Homepage presenter.
 */
class StitkyPresenter extends BasePresenter
{


    public function renderDefault() {
        $this->template->stitky = $this->clanky->getStitky();
    }

}
