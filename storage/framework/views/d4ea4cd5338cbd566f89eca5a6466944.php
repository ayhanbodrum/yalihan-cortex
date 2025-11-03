<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Yalıhan Emlak - Gayrimenkul'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #1e40af;
            --accent-color: #f59e0b;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        
        .footer {
            background: #1f2937;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer a {
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: white;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand text-primary fw-bold" href="<?php echo e(route('home')); ?>">
                <i class="fas fa-building me-2"></i>Yalıhan Emlak
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('home')); ?>">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('frontend.portfolio.index')); ?>">Portföy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('ilanlar.index')); ?>">İlanlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('advisors')); ?>">Danışmanlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('contact')); ?>">İletişim</a>
                    </li>
                </ul>
                
                <div class="d-flex gap-2">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('admin.dashboard.index')); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-user me-1"></i>Panel
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-1"></i>Giriş
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">Yalıhan Emlak</h5>
                    <p class="text-muted">
                        Türkiye'nin önde gelen gayrimenkul danışmanlık firması. 
                        Güvenilir, profesyonel ve müşteri odaklı hizmet anlayışımızla yanınızdayız.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Hızlı Linkler</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo e(route('home')); ?>">Ana Sayfa</a></li>
                        <li class="mb-2"><a href="<?php echo e(route('frontend.portfolio.index')); ?>">Portföy</a></li>
                        <li class="mb-2"><a href="<?php echo e(route('ilanlar.index')); ?>">İlanlar</a></li>
                        <li class="mb-2"><a href="<?php echo e(route('advisors')); ?>">Danışmanlar</a></li>
                        <li class="mb-2"><a href="<?php echo e(route('contact')); ?>">İletişim</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Hizmetler</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Satılık Evler</a></li>
                        <li class="mb-2"><a href="#">Kiralık Evler</a></li>
                        <li class="mb-2"><a href="#">Arsa</a></li>
                        <li class="mb-2"><a href="#">İşyeri</a></li>
                        <li class="mb-2"><a href="#">Yatırım Danışmanlığı</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">İletişim</h6>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span>Yalıkavak, Bodrum, Muğla</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <span>+90 252 123 45 67</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <span>info@yalihanemlak.com</span>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; <?php echo e(date('Y')); ?> Yalıhan Emlak. Tüm hakları saklıdır.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted me-3">Gizlilik Politikası</a>
                    <a href="#" class="text-muted">Kullanım Şartları</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/layouts/frontend.blade.php ENDPATH**/ ?>