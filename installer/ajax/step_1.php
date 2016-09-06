<?php
error_reporting(E_ERROR | E_PARSE);

session_start();
session_destroy(); 


/*
if (!session_is_registered("LANG")) 
{
    $lang = "en";
}
*/
	
?>

	
			<div class="progress">
				Step 1 out of 7 - Select your language			
			</div>
				<div class="sections">
					<div class="info">To begin, please select the preferred language, during the instalation, and click on "Next".</div>
						<div class="row">
							<label for="field_language" >
								Language																			
							</label>
							<div class="field">
								<select id="field_language" name="language"  class="select">
									<option value="en" selected="selected"> English</option>
									<option value="pt_BR" 	              > Portuguese - Brazil</option>
								</select>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="buttons">
						<button class="button button-next" id="button_step_1" onclick="step_2(document.getElementById('field_language').value)" >Next</button>
					</div>
			
			</div>
			<div class="clear"></div>

			