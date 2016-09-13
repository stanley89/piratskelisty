<?php

namespace Models;
use Sunra\PhpSimple\HtmlDomParser;
use Nette,
    Nette\Utils\Strings;


/**
 * Category management.
 */
class Rss extends \Nette\Object
{
	/** @var Nette\Database\Context */
    private $database;

	// convert to UTF-8 by dgx
	function autoUTF($s)
	{

		// detect UTF-8
		if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $s)) {
			return $s;
		}
		// detect binary
		if (preg_match('#[\x81\x83\x88\x90\x98]#', $s)) {
				return null;
		}
		// detect WINDOWS-1250
		if (preg_match('#[\x7F-\x9F\xBC]#', $s)) {
			return iconv('WINDOWS-1250', 'UTF-8', $s);
		}

		// assume ISO-8859-2
		//return iconv('ISO-8859-2', 'UTF-8', $s);
		// assume binary
		return null;
	}

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

	public function getChannelByKey($key) {
		return $this->database->fetch("select *"
				. " from rss_channels"
				. " where `key`=?;",$key);
	}

	public function getChannels() {
		return $this->database->fetchAll("SELECT *"
				. " FROM rss_channels;");
	}
	public function loadChannels() {
		$stop = $this->database->fetchField("select max(unix_timestamp(pub_date))>(unix_timestamp(now())-100) from rss_items;");
		if ($stop) return;
		$channels = $this->getChannels();
		foreach ($channels as $channel) {
			$page = file_get_contents($channel['link']);
			$html = HtmlDomParser::str_get_html( $page );
			foreach($html->find('.postbody .content a.postlink') as $element) {
				$exists = $this->getItemByLinkChannel($element->href, $channel->id);
				if (!empty($exists)) continue;
				$arr = array('link' => $element->href,
					'rss_channel_id' => $channel['id']);
				$clanek = $this->autoUTF(file_get_contents($element->href));
				if (empty($clanek)) continue;
				$dom = HtmlDomParser::str_get_html($clanek);
				if (empty($dom)) continue;
				$title = $dom->find('title',0);
				if (!empty($title)) {
					$arr['title'] = $title->innertext;
				} else {
					$arr['title'] = $arr['link'];
				}
				$description = $dom->find('meta[name=description]',0);
				if (!empty($description)) {
					$arr['description'] = $description->content;
				}

				$this->database->query('insert into rss_items ',$arr);
			}
		}
	}


	public function getItemsByKey($key, $limit=10) {
		$channel = $this->getChannelByKey($key);
		$items = $this->database->fetchAll("select *"
				. " from rss_items"
				. " where rss_channel_id=?"
				. " ORDER BY id desc"
				. " LIMIT ?",$channel['id'],$limit);
		return $items;
	}

	public function getItemByLinkChannel($link,$channel_id) {
		return $this->database->fetch("select *"
				. " from rss_items"
				. " where link=?"
				. " and rss_channel_id=?;",$link, $channel_id);
	}
}
