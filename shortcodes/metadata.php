<?php

class Metadata
{
    const activite = "activite";
    const cours = "cours";

    static function is_valid($atts)
    {
        return !empty($atts[Metadata::activite]) && !empty($atts[Metadata::cours]);
    }

    static function get_current_attributes()
    {
        $whole_content = get_the_content();
        $start_metadata_pos = strpos($whole_content, '[meta');
        $len_metadata = strpos($whole_content, "[/meta]") - $start_metadata_pos;
        $metadata_shortcode = substr($whole_content, $start_metadata_pos, $len_metadata);
        $metadata_shortcode = substr_replace($metadata_shortcode, " /", $len_metadata - 1, 0);
        $metadata_attributes = shortcode_parse_atts($metadata_shortcode);

        return $metadata_attributes;
    }
}

add_shortcode('meta', 'metadata_shortcode');
function metadata_shortcode($atts, $content)
{
    if (Metadata::is_valid($atts))
    {
        return '';
    }
    else
    {
        return '<div class="notice notice-error">
                <p>Balise metadata incorrecte, les attributs activite et cours doivent avoir des valeurs non nulles. 
                Exemple d\' une balise correcte : [meta activite="test1" cours="PPC"][\\meta]</p>
            </div>';
    }
}
