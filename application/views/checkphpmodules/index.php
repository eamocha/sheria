
    <style>
        body {
            background-color: #f8f9fa;
            padding-bottom: 50px;
        }
        .summary-card, .category-card {
            border: none;
            border-radius: 10px;
        }
        .module-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .module-icon {
            font-size: 1.5rem;
            width: 40px;
            text-align: center;
        }
        .status-badge {
            font-size: 0.7rem;
        }
        .progress {
            height: 8px;
        }
        .category-header {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
        }
        .search-box {
            max-width: 300px;
        }
        .nav-pills .nav-link.active {
            background-color: #6c757d;
        }
        #categoryFilter {
            max-height: 300px;
            overflow-y: auto;
        }
        .service-card {
            border-left: 4px solid #17a2b8;
        }
        .tabs-container {
            margin-bottom: 20px;
        }
        .tab-content {
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .nav-tabs .nav-item {
            margin-bottom: -1px;
        }
        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5em 0.8em;
        }
    </style>
<div class="container-fluid">
    <header class="text-center mb-5">
        <h1 class="display-4 font-weight-bold text-dark"><i class="fas fa-cubes mr-2"></i>Server Services Checker</h1>
        <p class="lead text-muted">Check which PHP modules and services are installed on your server</p>
    </header>

    <?php
    // Function to check if a command is available
    function command_exists($command) {
        $output = null;
        $result = null;
        exec("which $command", $output, $result);
        return $result === 0;
    }

    // Function to get version of a command
    function get_version($command, $version_flag = '--version') {
        $output = null;
        $result = null;
        exec("$command $version_flag 2>&1", $output, $result);
        return $result === 0 && !empty($output) ? $output[0] : 'Not available';
    }

    // Check additional services
    $services = [
        'LibreOffice' => [
            'exists' => command_exists('libreoffice'),
            'version' => get_version('libreoffice', '--version'),
            'description' => 'Document conversion and office suite'
        ],
        'SSL' => [
            'exists' => function_exists('openssl_verify') || command_exists('openssl'),
            'version' => function_exists('openssl_verify') ? OPENSSL_VERSION_TEXT : get_version('openssl', 'version'),
            'description' => 'Secure Sockets Layer for encrypted connections'
        ],
        'Node.js' => [
            'exists' => command_exists('node'),
            'version' => get_version('node', '--version'),
            'description' => 'JavaScript runtime built on Chrome\'s V8 JavaScript engine'
        ],
        'Docker' => [
            'exists' => command_exists('docker'),
            'version' => get_version('docker', '--version'),
            'description' => 'Platform for developing, shipping, and running applications in containers'
        ]
    ];

    $modules = [
        'Math' => [
            'bcmath' => 'BC Math'
        ],
        'Compression' => [
            'bz2' => 'BZip2',
            'zlib' => 'Zlib'
        ],
        'System' => [
            'calendar' => 'Calendar',
            'Core' => 'Core',
            'date' => 'Date/Time',
            'filter' => 'Input Filter',
            'hash' => 'Hash',
            'readline' => 'Readline',
            'Reflection' => 'Reflection',
            'session' => 'Session',
            'SPL' => 'SPL',
            'standard' => 'Standard',
            'tokenizer' => 'Tokenizer'
        ],
        'Text Processing' => [
            'ctype' => 'Ctype',
            'iconv' => 'Iconv',
            'mbstring' => 'Multibyte String',
            'pcre' => 'PCRE'
        ],
        'Web Services' => [
            'curl' => 'cURL',
            'soap' => 'SOAP',
            'xmlrpc' => 'XML-RPC'
        ],
        'XML' => [
            'dom' => 'DOM',
            'libxml' => 'LibXML',
            'SimpleXML' => 'SimpleXML',
            'xml' => 'XML',
            'xmlreader' => 'XMLReader',
            'xmlwriter' => 'XMLWriter'
        ],
        'Data Formats' => [
            'json' => 'JSON',
            'Phar' => 'Phar',
            'zip' => 'Zip'
        ],
        'Database' => [
            'mysqli' => 'MySQLi',
            'mysqlnd' => 'MySQL Native Driver',
            'PDO' => 'PDO',
            'pdo_mysql' => 'PDO MySQL',
            'pdo_sqlsrv' => 'PDO SQLSRV',
            'sqlsrv' => 'SQLSRV'
        ],
        'Security' => [
            'openssl' => 'OpenSSL',
            'mcrypt' => 'Mcrypt'
        ],
        'Multimedia' => [
            'exif' => 'EXIF',
            'gd' => 'GD'
        ],
        'Internationalization' => [
            'intl' => 'Internationalization'
        ],
        'Miscellaneous' => [
            'fileinfo' => 'Fileinfo',
            'ldap' => 'LDAP',
            'tidy' => 'Tidy',
            'ionCube Loader' => 'ionCube Loader'
        ]
    ];

    $installed_count = 0;
    $total_count = 0;

    // First pass to count modules
    foreach ($modules as $category => $items) {
        foreach ($items as $module => $name) {
            $total_count++;
            if (extension_loaded($module)) {
                $installed_count++;
            }
        }
    }

    $percentage = ($installed_count/$total_count)*100;
    $progress_class = $percentage == 100 ? 'bg-success' : ($percentage >= 70 ? 'bg-primary' : ($percentage >= 40 ? 'bg-warning' : 'bg-danger'));
    ?>

    <!-- Tabs for switching between PHP modules and Services -->
    <div class="tabs-container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="php-tab" data-toggle="tab" href="#php" role="tab" aria-controls="php" aria-selected="true">
                    <i class="fab fa-php mr-2"></i>PHP Modules
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="services-tab" data-toggle="tab" href="#services" role="tab" aria-controls="services" aria-selected="false">
                    <i class="fas fa-server mr-2"></i>Services
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <!-- PHP Modules Tab -->
            <div class="tab-pane fade show active" id="php" role="tabpanel" aria-labelledby="php-tab">
                <div class="card summary-card shadow-sm mb-5">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <h5 class="card-title font-weight-bold mb-3"><i class="fas fa-chart-pie mr-2"></i>Module Summary</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Progress</span>
                                    <span class="font-weight-bold"><?= round($percentage, 1) ?>%</span>
                                </div>
                                <div class="progress mb-4">
                                    <div class="progress-bar progress-bar-striped <?= $progress_class ?>" role="progressbar"
                                         style="width: <?= $percentage ?>%"
                                         aria-valuenow="<?= $installed_count ?>"
                                         aria-valuemin="0"
                                         aria-valuemax="<?= $total_count ?>">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center">
                                        <span class="badge badge-pill badge-success p-2"><i class="fas fa-check-circle mr-1"></i> <?= $installed_count ?> installed</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="badge badge-pill badge-secondary p-2"><i class="fas fa-times-circle mr-1"></i> <?= $total_count - $installed_count ?> missing</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="badge badge-pill badge-info p-2"><i class="fas fa-cube mr-1"></i> <?= $total_count ?> total</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pl-md-4">
                                <h5 class="card-title font-weight-bold mb-3"><i class="fas fa-server mr-2"></i>Server Information</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><strong><i class="fas fa-code mr-2 text-info"></i>PHP Version:</strong> <span class="float-right"><?= phpversion() ?></span></li>
                                    <li class="mb-2"><strong><i class="fas fa-server mr-2 text-info"></i>Server:</strong> <span class="float-right"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></span></li>
                                    <li class="mb-2"><strong><i class="fas fa-desktop mr-2 text-info"></i>System:</strong> <span class="float-right"><?= php_uname('s') ?> <?= php_uname('r') ?></span></li>
                                    <li class="mb-0"><strong><i class="fas fa-folder mr-2 text-info"></i>Document Root:</strong> <span class="float-right text-truncate" style="max-width: 200px;"><?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="moduleSearch" class="form-control" placeholder="Search modules...">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary" id="showAll">Show All</button>
                            <button type="button" class="btn btn-outline-secondary" id="showInstalled">Show Installed</button>
                            <button type="button" class="btn btn-outline-secondary" id="showMissing">Show Missing</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 d-none d-lg-block">
                        <div class="card shadow-sm sticky-top" style="top: 20px;">
                            <div class="card-header bg-light font-weight-bold">Categories</div>
                            <div class="card-body p-0" id="categoryFilter">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($modules as $category => $items): ?>
                                        <a href="#category-<?= preg_replace('/[^a-zA-Z0-9]/', '-', $category) ?>" class="list-group-item list-group-item-action"><?= $category ?> <span class="badge badge-pill badge-secondary float-right"><?= count($items) ?></span></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <?php foreach ($modules as $category => $items):
                            $category_id = preg_replace('/[^a-zA-Z0-9]/', '-', $category);
                            ?>
                            <div class="card category-card shadow-sm mb-4" id="category-<?= $category_id ?>">
                                <div class="card-header category-header py-3">
                                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-folder-open mr-2"></i><?= $category ?></span>
                                        <span class="badge badge-pill badge-light"><?= count($items) ?> modules</span>
                                    </h5>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <?php foreach ($items as $module => $name):
                                            $is_loaded = extension_loaded($module);
                                            $status = $is_loaded ? 'success' : 'danger';
                                            $icon = $is_loaded ? 'fa-check-circle' : 'fa-times-circle';
                                            $text = $is_loaded ? 'Installed' : 'Not Installed';
                                            ?>
                                            <div class="col-md-6 col-lg-6 col-xl-4 mb-3 module-item" data-status="<?= $status ?>">
                                                <div class="card module-card h-100 border-left-<?= $status ?>">
                                                    <div class="card-body py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="module-icon mr-3">
                                                                <i class="fas <?= $icon ?> text-<?= $status ?> fa-lg"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="card-title mb-1 font-weight-bold"><?= $name ?></h6>
                                                                <p class="card-text text-muted mb-1 small"><?= $text ?></p>
                                                                <small><code class="text-dark"><?= $module ?></code></small>
                                                            </div>
                                                            <span class="badge badge-<?= $status ?> status-badge py-1"><?= $text ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Services Tab -->
            <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-server mr-2"></i>Server Services</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($services as $service => $details):
                                        $status = $details['exists'] ? 'success' : 'danger';
                                        $icon = $details['exists'] ? 'fa-check-circle' : 'fa-times-circle';
                                        $text = $details['exists'] ? 'Available' : 'Not Available';
                                        ?>
                                        <div class="col-md-6 col-lg-3 mb-4">
                                            <div class="card service-card h-100 border-left-<?= $status ?>">
                                                <div class="card-body text-center">
                                                    <div class="mb-3">
                                                        <i class="fas <?= $icon ?> fa-3x text-<?= $status ?>"></i>
                                                    </div>
                                                    <h5 class="card-title"><?= $service ?></h5>
                                                    <p class="card-text text-muted small"><?= $details['description'] ?></p>
                                                    <div class="mt-3">
                                                        <span class="badge badge-<?= $status ?> badge-lg"><?= $text ?></span>
                                                    </div>
                                                    <div class="mt-3">
                                                        <small class="text-muted">Version:</small>
                                                        <div class="font-weight-light"><?= $details['version'] ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Service Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fab fa-libreoffice mr-2"></i>LibreOffice</h6>
                                        <p class="small">LibreOffice is a powerful office suite. It's used for document conversion and processing.</p>
                                        <h6><i class="fas fa-lock mr-2"></i>SSL</h6>
                                        <p class="small">SSL provides secure, encrypted communications between servers and clients.</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fab fa-node-js mr-2"></i>Node.js</h6>
                                        <p class="small">Node.js is a JavaScript runtime for building server-side and networking applications.</p>
                                        <h6><i class="fab fa-docker mr-2"></i>Docker</h6>
                                        <p class="small">Docker is a platform for developing, shipping, and running applications in containers.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 py-4 text-muted">
        <p>Server Services Checker &copy; <?= date('Y') ?></p>
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    jQuery(document).ready(function() {
        // Search functionality
        jQuery('#moduleSearch').on('keyup', function() {
            var value = jQuery(this).val().toLowerCase();
            jQuery('.module-item').each(function() {
                var text = jQuery(this).text().toLowerCase();
                jQuery(this).toggle(text.indexOf(value) > -1);
            });

            // Hide empty categories
            jQuery('.category-card').each(function() {
                var visibleItems = jQuery(this).find('.module-item:visible').length;
                jQuery(this).toggle(visibleItems > 0);
            });
        });

        // Filter buttons
        jQuery('#showAll').on('click', function() {
            jQuery('.module-item').show();
            jQuery('.category-card').show();
        });

        jQuery('#showInstalled').on('click', function() {
            jQuery('.module-item').hide();
            jQuery('.module-item[data-status="success"]').show();

            jQuery('.category-card').each(function() {
                var visibleItems = jQuery(this).find('.module-item:visible').length;
                jQuery(this).toggle(visibleItems > 0);
            });
        });

        jQuery('#showMissing').on('click', function() {
            jQuery('.module-item').hide();
            jQuery('.module-item[data-status="danger"]').show();

            jQuery('.category-card').each(function() {
                var visibleItems = jQuery(this).find('.module-item:visible').length;
                jQuery(this).toggle(visibleItems > 0);
            });
        });

        // Smooth scrolling for category links
        jQuery('a[href^="#category-"]').on('click', function(e) {
            e.preventDefault();
            var target = jQuery(this.getAttribute('href'));
            if (target.length) {
                jQuery('html, body').animate({
                    scrollTop: target.offset().top - 20
                }, 1000);
            }
        });
    });