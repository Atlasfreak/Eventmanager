<?=$this->layout("admin::layout", ["title" => "Admin-".$title, "no_logout" => isset($no_logout) ? $no_logout:false])?>

<h1 class="pt-3"><?=$this->e($title)?></h1>
<?=$this->section("description")?>
<hr>

<?=$this->section("content")?>