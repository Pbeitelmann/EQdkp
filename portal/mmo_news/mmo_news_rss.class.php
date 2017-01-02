<?php
/*	Project:	EQdkp-Plus
 *	Package:	MMO News Portal Module
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class mmo_news_rss extends gen_class {
	public static $shortcuts = array('puf'=>'urlfetcher');

	//Config
	var $cachetime			= 10800;	// refresh time in seconds default 3 hours = 10800 seconds
	var $tooltipcrop		= 60;		// after that number of symbols the text in the tooltip wraps
	var $titlecrop			= 30;		// after that number of symbols the text in the title wraps
	var $checkURL_first		= true ;
	var $blnWideContent		= false;
	var $moduleid			= 0;
	var $count				= 5;

	//return vars
	var $title 				= null;
	var $link 				= null;
	var $description		= null;
	var $lastcreate 		= null;
	var $feed				= null;
	var $news				= null;
	var $updated			= null;
	var $header				= '';
	var $output_left		= '';
	var $rssurl				= false;


	/**
	 * Constructor
	 *
	 * @return rss
	 */
	public function __construct($blnWideContent, $intCount = 5){
		//$this->blnWideContent = $blnWideContent;
		$this->blnWideContent = false;
		$this->header = ucfirst($this->config->get('default_game')).'-'.$this->user->lang('pm_mmo_news');
		
		$this->output = 'No feed for your game or language available.';

		switch($this->config->get('game_language')){
			case 'german': {
						switch (strtolower($this->config->get('default_game'))){
							case 'wow': 				$rss_number = 1 ; break;
							case 'daoc': 				$rss_number = 7 ; break;
							case 'eq': 					$rss_number = 10 ; break;
							case 'eq2': 				$rss_number = 10 ; break;
							case 'lotro': 				$rss_number = 4 ; break;
							case 'tr': 					$rss_number = 19 ; break;
							case 'vanguard': 			$rss_number = 5 ; break;
							case 'guildwars2': 			$rss_number = 52 ; break;
							case 'aoc': 				$rss_number = 13 ; break;
							case 'warhammer': 			$rss_number = 14 ; break;
							case 'aion': 				$rss_number = 22 ; break;
							case 'rom': 				$rss_number = 37 ; break;
							case 'swtor': 				$rss_number = 43 ; break;
							case 'diablo3': 			$rss_number = 39 ; break;
							case 'rift': 				$rss_number = 58 ; break;
							case 'tera': 				$rss_number = 55 ; break;
							case 'teso':				$rss_number = 75 ; break;
							
							default: $rss_number = 1 ;
								break;
						}
						
						$this->rssurl = 'http://rss.allvatar.com/news-'.$rss_number.'.xml';
			}
			break;
			
			case 'english': {
						switch (strtolower($this->config->get('default_game'))){
							case 'wow': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=15' ; break;
							case 'daoc': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=11' ; break;
							case 'eq': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=9' ; break;
							case 'eq2': 				$this->rssurl = 'https://forums.daybreakgames.com/eq2/index.php?forums/news-and-announcements.2/index.rss' ; break;
							case 'lotro': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=45' ; break;
							case 'vanguard': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=147' ; break;
							case 'guildwars2': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=473' ; break;
							case 'aoc': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=191' ; break;
							case 'warhammer': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=239' ; break;
							case 'aion': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=253' ; break;
							case 'rom': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=351' ; break;
							case 'swtor': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=367' ; break;
							case 'diablo3': 			$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=644' ; break;
							case 'rift': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=431' ; break;
							case 'tera': 				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=477' ; break;
							case 'tsw' :				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=404' ; break;
							case 'teso' :				$this->rssurl = 'http://www.mmorpg.com/gameRss.cfm?gameId=821' ; break;
						}
			}
			break;
			
			case 'spanish': {
						switch (strtolower($this->config->get('default_game'))){
							case 'wow': 				$this->rssurl = 'http://feeds.feedburner.com/guiaswowraideando' ; break;
						}
			}
			break;
		}

		if ($this->rssurl){
			$this->parseXML($this->GetRSS($this->rssurl));
			if ($this->news){
				$this->createOutput();
			}
		}
	}

	/**
	 * GetRSS get the RSS Feed from an given URL
	 * Check if an refresh is needed
	 *
	 * @param String $url must be an valid RSS Feed
	 * @return XMLString
	 */
	private function GetRSS($url){
		$rss_string = $this->pdc->get('portal.mmo_news.rss');
		if ($rss_string == null) {
			//nothing cached or expired
			$this->tpl->add_js('$.get("'.$this->server_path.'portal/mmo_news/update.php'.$this->SID.'");');
			//Is there some expired data?
			$expiredData = $this->pdc->get('portal.mmo_news.rss', false, false, true);
			$rss_string = ($expiredData != null) ? $expiredData : "";
		}

		return $this->decodeRSS($rss_string);
	}

	public function updateRSS(){
		$this->puf->checkURL_first = $this->checkURL_first;
		$rss_string = $this->puf->fetch($this->rssurl);
		$rss_string = is_utf8($rss_string) ? $rss_string : utf8_encode($rss_string);
		if ($rss_string && strlen($rss_string)>1){
			$this->pdc->put('portal.mmo_news.rss', @base64_encode($rss_string), $this->cachetime);
		} else {
			$this->pdc->put('portal.mmo_news.rss', "", $this->cachetime);
		}
	}

	private function decodeRSS($rss){
		if (!strlen($rss)) return '';
		$rss_string = @base64_decode($rss);
		return $rss_string;
	}

	/**
	 * parseXML
	 * parse the XML Data into an Array
	 *
	 * @param RSS-XML $rss
	 */
	private function parseXML($rss){
		if (!$rss OR $rss == 'ERROR') return false;
		$rss =  simplexml_load_string($rss);
		if(!is_object($rss)) return false;
		$this->title = $rss->channel->title;
		$this->link  = $rss->channel->link;
		$this->description = $rss->channel->description;
		$this->lastcreate =  $rss->channel->lastBuildDate;
		$this->feed	= $rss->channel->generator;
		$this->news = array();

		$count = ($this->count) ? intval($this->count) : 5;
		
		$i = 0;
		foreach($rss->channel->item as $item){
			if ($i >= $count){return;}
			$this->news[$i]['title']		= sanitize($item->title);
			$this->news[$i]['link']			= sanitize($item->link);
			$this->news[$i]['description']	= sanitize($item->description);
			$this->news[$i]['author']		= sanitize($item->author);
			$this->news[$i]['pubdate']		= sanitize($item->pubDate);
			$i++;
		}
	} # end function
	
	public function createOutput(){
		$updated_time = $this->time->user_date($this->updated, true, true);
		$this->header .= ' '.$updated_time;
		
		if (is_array($this->news)){
			foreach ($this->news as $key => $value){
				
			
				if($this->blnWideContent){
				
				} else {
					// Generate an array fo an accordion
					// array style: title => content
					$newstick_array[(string)$value['title']] = $this->createBody(
							$value['description'],
							$value['link'],
							$value['author'],
							$value['pubdate']
					);
				
				}
			}#  end foreach	
			
			//Output
			if($this->blnWideContent){
				
			} else {
				$this->output = '<div style="white-space:normal;">'.$this->jquery->accordion('rrs_news',$newstick_array).'</div>';
			}
			
		}
	}

	/**
	 * createLink
	 * Creates an link with the description in a tooltip.
	 *
	 * @param String $title
	 * @param String $link
	 * @param  String $disc
	 * @return String
	 */
	private function createLink($title,$link,$disc,$author="",$date="",$crop_title=false){
		$tt = str_replace('"', "'", $disc);
		$tt = str_replace(array("\n", "\r"), '', $tt);

		$header = "<b>".$title."</b><br />";
		$content = $this->wrapText($tt,$this->tooltipcrop) ;
		$footer = "<br />".date('d.m.Y, H:i', strtotime($date))." by <b>".$author."</b>";

		if ($crop_title){
			$title =	$this->cropText($title,$this->titlecrop) ;
		}
		$ret = "<a href='".sanitize($link)."' target='_blank' class='coretip' data-coretip='".sanitize($header.$content.$footer)."'>".sanitize($title)."</a>";
		return $ret ;
	}

	/**
	 * createBody
	 *
	 * @param  String $disc
	 * @param  String $author
	 * @param  String $date
	 * @return String
	 */
	private function createBody($disc,$link,$author="",$date=""){
		$content = '<a href="'.sanitize($link).'" target="_blank">'.sanitize($this->cropText($disc,280)).'</a>';
		$footer = $this->time->user_date(strtotime($date), true)." by <b>".sanitize($author)."</b>";
		return $content.'<br />'.$footer;
	}

	/**
	 * cropText
	 * crop the text after a given lenght
	 *
	 * @param String $text
	 * @param Integer $len
	 * @return String
	 */
	private function cropText($text,$len){
		$ret = "";
		$ret = substr($text,0,$len);
		if (strlen($text) > $len){
			$ret .= '..';
		}
		return $ret ;
	}

	/**
	 * wrapText
	 * wraps the text after a given lenght
	 * but only after an word!
	 *
	 * @param String $text
	 * @param Integer $len
	 * @return String
	 */
	private function wrapText($text,$len=60){
		$croplen = $len ;
		$ret = "";
		for($i=0;;$i++){
			if ($i == strlen($text)) {
				break;
			}

			$ret .= $text[$i];
			if ($i >= $len){
				if ($text[$i] == " "){
					$ret .= str_replace(' ', '<br />', $text[$i] );
					$len = $len+$croplen;
				}
			}
		}
		return $ret ;
	}
}
?>
