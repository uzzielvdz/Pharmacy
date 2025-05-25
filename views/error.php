<?php include dirname(__DIR__) . '/views/layouts/header.php'; ?>
<div class="card bg-gray-700 animate-slide-in">
    <div class="card-body">
        <h2 class="card-title text-2xl mb-4">Error</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger animate-slide-in" role="alert">
                <ul class="list-unstyled">
                    <?php foreach ($errors as $error): ?>
                        <li><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <p class="text-gray-400">Vuelve a <a href="<?php echo BASE_URL; ?>/public/index.php?controller=product&action=index" class="text-blue-400 hover:underline">Inicio</a>.</p>
    </div>
</div>
<?php include dirname(__DIR__) . '/views/layouts/footer.php'; ?>