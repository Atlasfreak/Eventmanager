<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
        <link href="<?=$this->e($path)?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?=$this->e($path)?>css/bootstrap-icons.css" rel="stylesheet">
        <link href="<?=$this->e($path)?>css/styles.css" rel="stylesheet">

        <?=$this->section("styles")?>

        <title><?=$this->e($title)?></title>
    </head>
    <body class="d-flex flex-column min-vh-100 ministry-teams" style="background: #f4f4f4;">
        <header class="sticky-top">
            <?=$this->section("header")?>
        </header>
        <main class="d-flex flex-column flex-fill">
            <div class="d-flex flex-column flex-fill container px-0 <?=$this->section("container_classes")?>">
                <div class="bg-white shadow p-3"><?=$this->section("content")?></div>
            </div>
        </main>
        <footer class="footer">
            <div class="container px-0">
                <div class="py-3 border-top text-muted d-flex justify-content-between align-items-center flex-wrap">
                    <span class="col-md-4 text-reset d-flex justify-content-center justify-content-md-start">
                        ©2021-<?=date("Y")?> Per Göttlicher
                        <?=$this->section("footer_left")?>
                    </span>
                    <span class="col-md-4 text-reset d-flex justify-content-center">
                        <a href="https://whgonline.de/pages/impressum.php" class="text-reset">Impressum</a>
                        <?=$this->section("footer_center")?>
                    </span>
                    <span class="col-md-4 text-reset d-flex justify-content-center justify-content-md-end">
                        <?=$this->section("footer_right")?>
                    </span>
                </div>
            </div>
        </footer>
        <script src="<?=$this->e($path)?>js/jquery.min.js"></script>
        <script src="<?=$this->e($path)?>js/bootstrap.min.js"></script>
        <?=$this->section("scripts")?>

    </body>
</html>