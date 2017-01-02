<?php
/*	Project:	EQdkp-Plus
 *	Package:	WoWprogress Portal Module
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if(!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

class wowprogress_portal extends portal_generic {
	public static $shortcuts = array('puf'	=> 'urlfetcher');

	protected static $path = 'wowprogress';
	protected static $data = array(
		'name'			=> 'wowprogress',
		'version'		=> '0.4.1',
		'author'			=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Shows the WoW Guildprogress',
		'lang_prefix'	=> 'wowprogress_',
		'icon'			=> 'fa-bar-chart-o',
	);

	private $tiers = array(
		'tier8', 'tier9_10', 'tier9_25', 'tier10_10', 'tier10_25', 'tier11', 'tier11_10',
		'tier11_25','tier12','tier12_10','tier12_25', 'tier13', 'tier13_10','tier13_25',
		'tier14','tier14_10','tier14_25','tier15', 'tier15_10', 'tier15_25','tier16',
		'tier16_10', 'tier16_25', 'tier17', 'tier18', 'tier19'
	);

	protected static $apiLevel = 20;

	public function get_settings($state){
		$arrTiers;
		//Build Tear Multiselect
		foreach($this->tiers as $strTier){
			$strNumbers = str_replace("tier", "", $strTier);
			$arrNumbers = explode("_", $strNumbers);

			$arrTiers[$strTier] = $this->user->lang('wp_tier').' '.$arrNumbers[0].((isset($arrNumbers[1])) ? ' ('.$arrNumbers[1].')' : '');
		}

		$settings	= array(
			'encounter' => array(
				'type'		=> 'multiselect',
				'options'	=> $arrTiers,
			),
			'banner' => array(
				'type'		=> 'dropdown',
				'options'	=> array(
					''			=> $this->user->lang('no'),
					'realm'		=> 'Realm Rank',
					'region'	=> strtoupper($this->config->get('uc_server_loc')).' Rank',
					'world'		=> 'World Rank',
				),
				'dependency'=> array(
					'realm'		=> array('guild_id'),
					'region'	=> array('guild_id'),
					'world'		=> array('guild_id'),
				),
			),
			'guild_id' => array(
				'type'		=> 'text',
				'size'		=> 12,
				'dir_help'	=> $this->user->lang('wowprogress_f_guild_id_help'),
			),
		);
		return $settings;
	}

	public function output() {
		if ($this->game->get_game() != "wow") return $this->user->lang('wp_wow_only');

		$strOut = $this->pdc->get('portal.module.wowprogress',false,true);

		if($strOut === NULL){
			if($this->config('banner') != ''){
				switch($this->position){
					case 'middle':
					case 'bottom':
						$strOut = '<center><a href="'.$this->buildURL('guild').'"><img alt="WoW Guild Rankings" src="'.$this->getImage($this->buildURL('horizontal')).'" border="0" /></a></center>';
						break;
					default:
						$strOut = '<center><a href="'.$this->buildURL('guild').'"><img alt="WoW Guild Rankings" src="'.$this->getImage($this->buildURL('vertical')).'" border="0" /></a></center>';
						break;
				}

			}else{
				$strOut = '<table class="table fullwidth colorswitch">';
				foreach($this->config('encounter') as $strKey){
					$strResult = $this->puf->fetch($this->buildURL($strKey));
					$arrResult = json_decode($strResult, true);
					if ($arrResult != NULL){
						$strNumbers = str_replace("tier", "", $strKey);
						$arrNumbers = explode("_", $strNumbers);

						$strOut.='<tr>';
						$strOut.='<th colspan="2">'.$this->user->lang('wp_ranking').' '.$this->user->lang('wp_tier').' '.$arrNumbers[0];
						if(isset($arrNumbers[1])) $strOut .= ' - '.$arrNumbers[1].' '.$this->user->lang('wp_man');
						$strOut.='</th>';
						$strOut.='<tr><td>'.$this->user->lang('wp_world').'</td><td>'.sanitize($arrResult["world_rank"]).'</td></tr>';
						$strOut.='<tr><td>'.strtoupper($this->config->get('uc_server_loc')).'-'.$this->user->lang('wp_rank').'</td><td>'.sanitize($arrResult["area_rank"]).'</td></tr>';
						$strOut.='<tr><td>'.$this->user->lang('wp_realm').'</td><td>'.sanitize($arrResult["realm_rank"]).'</td></tr>';
						$strOut.='</tr>';
					}
				}
				$strOut .= '</table>';
			}

			$this->pdc->put('portal.module.wowprogress',$strOut,3600,false,true);
		}
		return $strOut;
	}

	private function buildURL($strType=''){
		$url	= 'http://www.wowprogress.com/';
		$search	= array('+',"'",' ');
		$server	= urlencode(strtolower(str_replace($search, '-', unsanitize($this->config->get('servername')))));
		$guild	= str_replace($search, '+', urlencode(utf8_strtolower(unsanitize($this->config->get('guildtag')))));
		$locate	= $this->config->get('uc_server_loc');
		$region = $this->config('banner');

		switch($strType){
			case 'vertical':
				$url .= 'guild_img/'.$this->config('guild_id').'/out/type.site/guild_rank.'.$region;
				break;
			case 'horizontal':
				$url .= 'guild_img/'.$this->config('guild_id').'/out/type.forum/guild_rank.'.$region.'/ach_rank.'.$region;
				break;
			case 'guild':
				$url .= 'guild/'.$locate.'/'.$server.'/'.$guild.'/';
				break;
			default:
				$url .= 'guild/'.$locate.'/'.$server.'/'.$guild.'/'.'rating.'.$strType.'/json_rank';
				break;
		}
		return $url;
	}

	private function getImage($strImageURL){
		$strImageDir = $this->pfh->FolderPath('', 'wowprogress');
		$strCacheFile = $strImageDir.md5($strImageURL).'.png';
		//1 hour Cache
		if(file_exists($strCacheFile) && (filemtime($strCacheFile)+3600 > time())){
			return $this->pfh->FolderPath('', 'wowprogress', 'absolute').md5($strImageURL).'.png';
		} else {
			$image = $this->puf->fetch($strImageURL);
			if($image){
				$this->pfh->putContent($strCacheFile, $image);
				return $this->pfh->FolderPath('', 'wowprogress', 'absolute').md5($strImageURL).'.png';
			}
		}

		return false;
	}

}
?>
