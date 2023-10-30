<?php
$path = '/home/antar88/pear/share/pear';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
include('Mail.php');

$errors = array();                          // array to hold validation errors
$data = array();                            // array to pass back data
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST = array(
        "name" => "Nom",
        "guest" => "Invitat",
        "kids" => "Nens",
        "bus" => "Autobus",
        "song" => "Cançó"
    );
    $name = stripslashes(trim($_POST['name']));
    $guest = stripslashes(trim($_POST['guest']));
    $kids = stripslashes(trim($_POST['kids']));
    $bus = stripslashes(trim($_POST['bus']));
    $song = stripslashes(trim($_POST['song']));

    if (empty($name)) {
        $errors['name'] = 'Com et dius?';
    }

    if (empty($bus)) {
        $errors['bus'] = 'Necessitem saber si vas en bus.';
    }

    // if there are any errors in our errors array, return a success boolean or false
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {
        $subject = '[Casament] Ens ha contactat ' . $name . '.';
        $body = 'Ens han confirmat assistencia! <br><br>
                <strong>Nom: </strong>'.$name.'<br />
                <strong>Acompanyant: </strong>'.$guest.'<br />
                <strong>Nens: </strong>'.$kids.'<br />
                <strong>Bus: </strong>'.$bus.'<br />
                <strong>Cançó: </strong>'.$song.'<br />
        ';

        $conf = json_decode(shell_exec('gcloud secrets versions access latest --secret=mail_config'), true);
        $from = "Web del casament <antar@antarmf.com>";
        $to = $conf["to"];
        $host = $conf["host"];
        $username = $conf["username"];
        $password = $conf["password"];
        $headers = [
            'From' => $from,
            'To' => $to,
            'Subject' => $subject,
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=utf-8',
            'Content-Transfer-Encoding' => 'quoted-printable'
        ];

        $smtp = Mail::factory('smtp',
                array (
                        'host' => $host,
                        'port' => 587,
                        'auth' => true,
                        'username' => $username,
                        'password' => $password
                )
        );

        $mail = $smtp->send($to, $headers, $body);

        if (PEAR::isError($mail)) {
            $data['success'] = false;
            $data['errors']['general'] = "Hi ha hagut un problema enviant el formulari. Si us plau reintenta-ho o envia'ns un whwatsapp.";
        }
        else {
            $data['success'] = true;
            $data['message'] = 'Missatge enviat correctament';
        }
    }
    echo json_encode($data);
}  