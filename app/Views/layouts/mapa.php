<!DOCTYPE html>
<html lang="en">
<body>
<?= $this->extend('layouts/header') ?>
<?=$this->section('content')?>
<?= var_dump($evento['name_file']) ?>
<img class="logo-compact" src="<?= base_url('public/uploads/planos/' . $evento['name_file']) ?>" alt="">
</body>
<?=$this->endSection(); ?>