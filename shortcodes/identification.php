<?php

class Identification
{
    const NUMERO = "numero";
}

function login_form_html($title, $extra_html = "")
{
    $login = Localisation::get('Se connecter');

    $numero_str = Identification::NUMERO;

    $content = '<div>
                    <form method="POST" action="">
                        <label for="' . $numero_str . '">Numero</label>
                        <input type="text" name="' . $numero_str . '">
                        <button type="submit">' . $login . '</button>
                    </form>
                    ' . $extra_html . '
                </div>';

    $html = HtmlGen::fieldset($title, $content);

    return $html;
}

function logout_form_html($title, $numero)
{
    $logout = Localisation::get('Se deconnecter');
    $a = PSUser::get_numero();
    $url = plugins_url('personalised_support/student-summary.php');
    $content = '<div>
                    Connecté avec le numéro ' . $numero . '
                    <form method="POST" action="">
                    <br>
                        <button type="submit" name="logout">' . $logout . '</button>
                        <button><a href ="'.$url.'/'.$a.'" target="_blank" style="text-decoration: none; color: currentColor !important;">Synthese</a></button>
                    </form>
                </div>';

    $html = HtmlGen::fieldset($title, $content);

    return $html;
}

function wrong_numero_form_html($title, $numero)
{
    return login_form_html($title, '<div>Numéro ' . $numero . ' non existant.</div>');
}

add_shortcode('ident', 'identification_shortcode');
function identification_shortcode($atts, $content)
{
    $title = Localisation::get('Identification');

    $html = "";

    if (PSUser::has_valid_numero())
    {
        $is_user_logging_out = isset($_POST['logout']);
        if ($is_user_logging_out)
        {
            PSUser::set_numero(null);

            $html = login_form_html($title);
        }
        else
        {
            $html = logout_form_html($title, PSUser::get_numero());
        }
    }
    else
    {
        $is_user_trying_to_login = isset($_POST[Identification::NUMERO]);
        if ($is_user_trying_to_login)
        {
            $posted_numero = $_POST[Identification::NUMERO];
            if (PSUsers::is_numero_valid($posted_numero))
            {
                PSUser::set_numero($posted_numero);

                $html = logout_form_html($title, $posted_numero);
            }
            else
            {
                $html = wrong_numero_form_html($title, $posted_numero);
            }
        }
        else
        {
            $html = login_form_html($title);
        }
    }

    return $html;
}
