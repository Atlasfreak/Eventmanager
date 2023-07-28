<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" type="image/x-icon" href="<?= $this->e($path) ?>favicon.ico" />
    <link href="<?= $this->e($path) ?>css/bootstrap.min.css?v=5.3.1" rel="stylesheet">
    <link href="<?= $this->e($path) ?>css/bootstrap-icons.min.css?v=1.10.5" rel="stylesheet">
    <link href="<?= $this->e($path) ?>css/styles.css?v=1.3" rel="stylesheet">

    <?= $this->section("styles") ?>

    <title><?= $this->e($title) ?></title>
</head>

<body class="d-flex flex-column min-vh-100" style="background: #f4f4f4;">
    <header class="sticky-top">
        <?= $this->section("header") ?>
    </header>
    <main class="d-flex flex-column flex-fill">
        <div class="container-xl px-0 <?= $this->section("container_classes") ?>">
            <?= $this->section("content") ?>
        </div>
    </main>
    <footer class="footer">
        <div class="container-xl px-0">
            <div
                class="p-3 border-top text-body-secondary d-flex justify-content-between align-items-center flex-column flex-md-row">
                <span class="col-md-4 text-reset d-flex justify-content-center justify-content-md-start">
                    <a href="<?= $this->e($path) ?>copyright.php" class="text-reset">©2022-<?= date("Y") ?> Per
                        Göttlicher</a>
                    <?= $this->section("footer_left") ?>
                </span>
                <span class="col-md-4 text-reset d-flex justify-content-center">
                    <a href="<?= $this->e(CONFIG_DATA["general"]["impressum_url"]) ?>" class="text-reset">Impressum</a>
                    <?= $this->section("footer_center") ?>
                </span>
                <span class="col-md-4 text-reset d-flex justify-content-center justify-content-md-end">
                    <?= $this->section("footer_right") ?>
                </span>
            </div>
        </div>
    </footer>
    <script src="<?= $this->e($path) ?>js/jquery.min.js"></script>
    <script src="<?= $this->e($path) ?>js/bootstrap.bundle.min.js?v=5.3.1"></script>
    <?= $this->section("scripts") ?>
</body>

</html>