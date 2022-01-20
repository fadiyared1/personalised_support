<?php

class HtmlGen
{
	static function fieldset($title_legend, $content)
	{
		return '<fieldset class="formSlider">
					<legend class="applicationForm__text">' . $title_legend . '</legend>
					' . $content . '
				</fieldset>';
	}
}
