<?php


namespace Models;

use Nette,
    Nette\Utils\Strings;


class Upload extends \Nette\Object
{
    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function get($id) {
        return $this->database->fetch("SELECT * FROM upload WHERE id = ?;",$id);
}
    public function getPairs() {
        
        return $this->database->fetchPairs("SELECT id, alt from upload order by id desc;");
    }
    public function getAll() {
        return $this->database->fetchAll("SELECT * FROM upload order by id desc;");
    }
    public function getImages() {
        return $this->database->fetchAll("SELECT * FROM upload
                                          WHERE extension='jpg' OR extension='jpeg' OR extension='png'
                                          order by id desc;");
    }

    public function save($vals) {
		$arr = array('alt' => $vals['alt'],
            'title' => $vals['title'],
        );

		if (!empty($vals['id'])) {
			$this->database->query("UPDATE upload SET ",$arr," WHERE id=?;",$vals['id']);
			return $this->get($vals['id']);
		} else {
                        $arr['extension'] = $vals['extension'];
			$this->database->query("INSERT INTO upload ",$arr);
			return $this->get($this->database->getInsertId());
		}
	}
}