<?php
/**
 * Created by PhpStorm.
 * User: Simen Bai - Bai Media
 * Date: 14-Sep-18
 * Time: 16:55
 */

require 'vendor/autoload.php';
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
</head>
<body>

<button id="edit" class="btn btn-primary" onclick="edit();" type="button">Edit</button>
<form class="d-inline" method="post" action="">
    <button class="btn btn-primary" type="submit">Lagre post</button>
    <textarea name="post" class="click2edit d-block" style="
        background-color:transparent;
        border: 0;
        font-size: 1em;
        resize: none;
        height: 0;
    "></textarea>
</form>
<?php
//Gets all the posts from the database
$allPosts = sqlUtils::getAllPosts();

//Checks if there is any posts returned
if (!empty($allPosts)) {
    //Runs through each post and posts the information about it
    foreach ($allPosts as $post) {
        $date = new DateTime($post["poststamp"]);
        ?>
        <br>
        <?= $date->format('H:i - d.m.Y') ?>
        <br/>
        <?= isset($post["username"]) ? $post["username"] : "Ukjent bruker" ?>
        <?= $post["contents"] ?>
        <br>
        <hr>
        <?php
    }
}
?>
<script src="assets/js/summernote-image-attributes.js"></script>
<script>

    $(document).ready(function () {
        $.ajax({
            url: 'https://api.github.com/emojis',
            async: false
        }).then(function (data) {
            window.emojis = Object.keys(data);
            window.emojiUrls = data;
        });
    });


    function edit() {
        $('.click2edit').summernote({
            focus: true,
            lang: 'nb-NO',
            hint: {
                match: /:([\-+\w]+)$/,
                search: function (keyword, callback) {
                    callback($.grep(emojis, function (item) {
                        return item.indexOf(keyword) === 0;
                    }));
                },
                template: function (item) {
                    var content = emojiUrls[item];
                    return '<img src="' + content + '" width="20" /> :' + item + ':';
                },
                content: function (item) {
                    var url = emojiUrls[item];
                    if (url) {
                        return $('<img />').attr('src', url).css('width', 20)[0];
                    }
                    return '';
                }
            },
            popover: {
                image: [
                    ['custom', ['imageAttributes']],
                    ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                ],
            },
            imageAttributes: {
                icon: '<i class="note-icon-pencil"/>',
                removeEmpty: false, // true = remove attributes | false = leave empty if present
                disableUpload: false // true = don't display Upload Options | Display Upload Options
            }
        });
    }

    function save() {
        let markup = $('.click2edit').summernote('code');
        console.log(markup);
        $('.click2edit').summernote('destroy');
    }
</script>

</body>
</html>