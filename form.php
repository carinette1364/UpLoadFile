<?php
// var_dump($_POST);

$errors = [];
$user_lastname = $user_firstname = $user_mail = $user_tel = '';

function cleanPost($datapost)
{
    $datapost = trim($datapost);
    $datapost = stripslashes($datapost);
    return htmlspecialchars($datapost);
}
// Je vérifie que le formulaire est soumis, comme pour tout traitement de formulaire.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Securité en php
    // var_dump($_POST);
    $user_lastname = cleanPost($_POST['user_lastname']);
    $user_firstname = cleanPost($_POST['user_firstname']);
    $user_mail = cleanPost($_POST['user_mail']);
    $user_tel = cleanPost($_POST['user_tel']);
    if (preg_match("/^[A-Z][a-zA-Z -]+$/", $user_lastname) == false)
        $errors[] = '<p>ce nom : ' . $user_lastname . ' n\'est pas valide.</p>';
    if (preg_match("/^[A-Z][a-zA-Z -]+$/", $user_firstname) == false)
        $errors[] = '<p>ce prénom : ' . $user_firstname . ' n\'est pas valide.</p>';
    if (filter_var($user_mail, FILTER_VALIDATE_EMAIL) == false)
        $errors[] = '<p>cet email : ' . $user_mail . ' n\'est pas valide.</p>';
    if (preg_match("/^[0-9]{10}+$/", $user_tel) == false)
        $errors[] = '<p>ce numéro : ' . $user_tel . ' n\'est pas valide.</p>';


    // chemin vers un dossier sur le serveur qui va recevoir les fichiers uploadés (attention ce dossier doit être accessible en écriture)
    $uploadDir = 'public/uploads/';
    // le nom de fichier sur le serveur est ici généré à partir du nom de fichier sur le poste du client (mais d'autre stratégies de nommage sont possibles)
    $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
    // Je récupère l'extension du fichier
    $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    // Les extensions autorisées
    $authorizedExtensions = ['jpg', 'png', 'gif', 'webp'];
    // Le poids max géré par PHP par défaut est de 2M ici on le définit à 1M
    $maxFileSize = 1000000;

    // Je sécurise et effectue mes tests

    /****** Si l'extension est autorisée *************/
    if ((!in_array($extension, $authorizedExtensions))) {
        $errors[] = 'Veuillez sélectionner une image de type Jpg ou Png ou Gif ou Webp !<br>' . PHP_EOL;
    }

    /****** On vérifie si l'image existe et si le poids est autorisé en octets *************/
    if (file_exists($_FILES['avatar']['tmp_name']) && filesize($_FILES['avatar']['tmp_name']) > $maxFileSize) {
        $errors[] = "Votre fichier doit faire moins de 1M !";
    }

    /****** Si je n'ai pas d"erreur alors j'upload *************/

    if (empty($errors)) {
        $uniqueName = uniqid('', true);
        $file = $uniqueName . "." . $extension;
        // var_dump($file);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);
        // echo uniqid($_FILES['avatar']['tmp_name']);
        echo '<p>Well done, ' . $user_firstname . ' , your profile image is uploaded !</p>';
?>      <section>
        <div><img src="<?php echo 'public/uploads/' . basename($_FILES['avatar']['name']); ?>" alt="profile image" width='200' height='auto' /></div>
<?php
        echo '<ul>Your informations:<br>' . PHP_EOL;
        // var_dump($_POST);
        foreach ($_POST as $key => $value) {
            echo '<li>' . $value . '</li>' . PHP_EOL;
        }
        echo '</ul></section>' . PHP_EOL;
        
    } else {
        // var_dump($errors);
        echo '<p> Hey ' . $user_firstname . ' : Choose another profile image please!<br>' . PHP_EOL;
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <form method="post" enctype="multipart/form-data">
        <div class="form-label-input">
            <label for="firstname">Firstname :</label>
            <input type="text" id="firstname" name="user_firstname" size="32" placeholder="Bernard">
        </div>
        <div class="form-label-input">
            <label for="lastname">Lastname :</label>
            <input type="text" id="lastname" name="user_lastname" size="32" placeholder="Dupont">
        </div>
        <div class="form-label-input">
            <label for="mail">E-mail&nbsp;:</label>
            <input type="email" id="mail" name="user_mail" size="32" placeholder="bernard@gmail.com">
        </div>
        <div class="form-label-input">
            <label for="tel">Phone&nbsp;:</label>
            <input type="tel" id="tel" name="user_tel" size="32" placeholder="0655112288">
        </div>
        <div class="form-label-input">
            <label for="imageUpload">Upload an profile image</label>
            <input type="file" name="avatar" id="imageUpload" />
        </div>
        <div id="button">
            <button name="send" id="send">Send</button>
            <button name="delete" id="delete">Delete</button>
        </div>
    </form>
</body>

</html>