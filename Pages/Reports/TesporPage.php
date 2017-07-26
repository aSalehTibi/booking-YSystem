<?php
/**
Copyright 2011-2015 Nick Korbel

This file is part of Booked Scheduler.

Booked Scheduler is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Booked Scheduler is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Booked Scheduler.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once(ROOT_DIR . 'Pages/Page.php');
require_once(ROOT_DIR . 'Pages/SecurePage.php');
require_once(ROOT_DIR . 'Pages/Reports/IDisplayableReportPage.php');
require_once(ROOT_DIR . 'Pages/Ajax/AutoCompletePage.php');
require_once(ROOT_DIR . 'Domain/Access/GroupRepository.php');


class TesporPage extends ActionPage
{
	public function __construct()
	{
		parent::__construct('Abrechnung',1);
	}

	public function PageLoad()
	{
		#$this->Set('RemindersPath', realpath(ROOT_DIR . 'Jobs/sendreminders.php'));
		$user_repo = new GroupRepository();
		$groups = array();
		foreach($user_repo->GetList()->Results() as $obj){
			$groups[] = array($obj->Id(), $obj->Name());
		}
		$this->Set('groups', $groups);

		#$helpType = $this->GetQuerystring('ht');
		$this->DisplayLocalized('Test-purpose.tpl');
	}
	/**
	 * @return void
	 */
	public function ProcessAction(){
		$this->PageLoad();
	}

	/**
	 * @param $dataRequest string
	 * @return void
	 */
	public function ProcessDataRequest($dataRequest){

	}

	/**
	 * @return void
	 */
	public function ProcessPageLoad(){
		$this->PageLoad();
	}
}
?>
