<?php
/*	Project:	EQdkp-Plus
 *	Package:	Guildbanker Plugin
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

if(!defined('EQDKP_INC'))
{
	header('HTTP/1.0 Not Found');
	exit;
}

if(!class_exists('gb_money')) {
	class gb_money extends gen_class {

		public function __construct(){
			$gamefile_moneydata	= $this->game->callFunc('guildbank_money', array());
			if($gamefile_moneydata){
				$this->data				= $gamefile_moneydata;
				$this->imagefolder	= 'games/'.$this->game->get_game().'/guildbank/';
			}else{
				$f_include				= $this->root_path.'plugins/guildbank/includes/gb_money.defaultconfig.php';
				include($f_include);
				$this->data				= $money_data;
				$this->imagefolder	= '';
			}
			$this->load_css();
		}

		private function load_css(){
			$this->tpl->add_css("
				.coin{
					font-size:10px;
				}
				.coin-large{
					font-size:14px;
				}
				.faicon{
					font-size:15px;
				}
				.faicon-large{
					font-size:20px;
				}
				.coin-platin{
					color:#EDEDEF;
				}
				.coin-platin .coin-inner{
					color:#D0D4D5;
				}
				.coin-gold{
					color:#DAA520;
				}
				.coin-gold .coin-inner{
					color:#FFD700;
				}
				.coin-silver{
					color:#a7a7a7;
				}
				.coin-silver .coin-inner{
					color:#dadada;
				}
				.coin-bronze{
					color:#cd7f32;
				}
				.coin-bronze .coin-inner{
					color:#e8c4a0;
				}
				img.moneysvg {
					width: 20px;
					height: 20px;
				}
				img.moneysvg-large {
					width: 28px;
					height: 28px;
				}"
			);
		}

		public function loadMoneyClass(){
			// this is just a pseudo class for the PDH to load the CSS in this construct... The PDH modules & the caching require this step
			return true;
		}

		public function get_data(){
			return $this->data;
		}

		public function output($input, $variables){
			if($input){
				$outp = floor($input/$variables['factor']);
				return ($variables['size'] == 'unlimited') ? $outp : substr($outp, ((isset($variables['size']) && is_int($variables['size']) && $variables['size'] > 0) ? -$variables['size'] : -2));
			}
			return '0';
		}

		public function input($arrData= false, $name='money_{ID}'){
			$total	= 0;
			foreach($this->data as $mname=>$value){
				if($arrData){
					$total		+= ($arrData[str_replace('{ID}', $mname, $name)]) ? ($arrData[str_replace('{ID}', $mname, $name)]*$value['factor']) : 0;
				}else{
					$total		+= ($this->in->exists(str_replace('{ID}', $mname, $name))) ? ($this->in->get(str_replace('{ID}', $mname, $name), 0)*$value['factor']) : 0;
				}

			}

			// add the operator..
			if($this->in->exists(str_replace('{ID}', 'pm', $name))){
				$total = $this->in->get(str_replace('{ID}', 'pm', $name)).$total;
			}

			return $total;
		}

		public function fields($mymoney){
			$monvalue = array();
			foreach($this->data as $monName=>$monValue){
				$monvalue[] = $this->output($mymoney, $monValue).$this->image($monValue);
			}
			return implode(" ", $monvalue);
		}

		public function image($monValue, $large=false, $size=false){
			$imgsize	= ($size) ? ' width="'.$size.'"' : '';
			switch($monValue['icon']['type']){
				case 'image':
					$output			= '<img src="'.$this->server_path.$this->imagefolder.$monValue['icon']['name'].'" class="'.(($large) ? 'moneysvg-large' : 'moneysvg').'" alt="'.$monValue['language'].'" title="'.$monValue['language'].'" />';
				break;
				case 'icon':
					$output			= '<span title="'.$monValue['language'].'"><i class="fa fa-'.$monValue['icon']['name'].' '.(($large) ? 'faicon-large' : 'faicon').'"></i></span>';
				break;
				default:
					$large_tag		= ($large) ? ' coin-large' : '';
					$output			= '<span class="fa-stack fa-fw coin coin-'.$monValue['icon']['name'].$large_tag.'" title="'.$monValue['language'].'"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-star fa-stack-1x fa-inverse coin-inner"></i></span>';
				break;
			}
			return $output;
		}

		public function editfields($mymoney=0, $name='money_{ID}', $plusminus=false, $readonly=false){
			$monvalue = ($plusminus) ? new hdropdown(str_replace('{ID}', 'pm', $name), array('options' => array('+'=>'+', '-'=>'-'))) : '';
			foreach($this->data as $monName=>$monValue){
				$monvalue .= $this->image($monValue).' '.new htext(str_replace('{ID}', $monName, $name), array('value' => $this->output($mymoney, $monValue), 'size' => (($monValue['size'] == 'unlimited') ? 9 : $monValue['size']), 'attrdata' => array('value' => $this->output($mymoney, $monValue)), 'readonly'=>$readonly, 'class'=>'money'));
				if($readonly){
					$monvalue .= new hhidden(str_replace('{ID}', $monName, $name), array('value' => $this->output($mymoney, $monValue), 'size' => (($monValue['size'] == 'unlimited') ? 9 : $monValue['size']), 'attrdata' => array('value' => $this->output($mymoney, $monValue)), 'class'=>'money'));
				}
			}
			return $monvalue;
		}
	}
}

?>
