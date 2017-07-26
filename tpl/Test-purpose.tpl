{*
Copyright 2012-2015 Nick Korbel

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
*}
{include file='globalheader.tpl' cssFiles="css/reports.css,scripts/js/jqplot/jquery.jqplot.min.css"}

<h1> Automatic report for all Groups </h1>
<fieldset id="customReportInput-container">
    <form id="customReportInput" action="../Test-purpose3.php" method="post">
       
        <div id="custom-report-input">
            {*
            <div class="input-set" id="selectDiv">
                <span class="label">Projekt </span>
                <select class="textbox" name="group_id">
                    <option value="0">{translate key=AllGroups}</option>
                    {foreach from=$groups item=group}
                        <option value="{$group[0]}">{$group[1]}</option>
                    {/foreach}
                </select>
            </div>
               *}
            <div class="input-set">
                <label for="range_within" style="width:auto;">{translate key=Between}</label>
                <input type="input" class="textbox dateinput" name="startDate" id="startDate"/> -
                <input type="hidden" id="formattedBeginDate"/>
                <input type="input" class="textbox dateinput" name="endDate" id="endDate"/>
                <input type="hidden" id="formattedEndDate"/>
            </div>
           
            <div class="input-set">
                <label for="range_within" style="width:auto;">Format</label><br/>
                <input type="radio" name="format" value="3" checked="checked"/> Summiert nach Ressource <br/>
                <input type="radio" name="format" value="4" /> Details (für Profs.)<br/>
                <input type="radio" name="format" value="5" /> users sind nicht Mitglieder in eine Gruppe<br/>
                
            </div>
             
        <input type="submit" class="button" value="generate" />
         {csrf_token} 
    </form>
</fieldset>


{jsfile src="autocomplete.js"}
{jsfile src="ajax-helpers.js"}
{jsfile src="reports/generate-reports.js"}
{jsfile src="reports/common.js"}
{jsfile src="reports/chart.js"}

<script type="text/javascript">
    $(document).ready(function () {
        var reportOptions = {
            userAutocompleteUrl: "{$Path}ajax/autocomplete.php?type={AutoCompleteType::User}",
            groupAutocompleteUrl: "{$Path}ajax/autocomplete.php?type={AutoCompleteType::Group}",
        };

        var reports = new GenerateReports(reportOptions);
        reports.init();

        var common = new ReportsCommon(
                {
                    scriptUrl: '{$ScriptUrl}'
                }
        );
        common.init();
    });
</script>


{control type="DatePickerSetupControl" ControlId="startDate" AltId="formattedBeginDate"}
{control type="DatePickerSetupControl" ControlId="endDate" AltId="formattedEndDate"}

{include file='globalfooter.tpl'}
