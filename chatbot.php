<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include __DIR__ . "/Trang_Chu_Includes/connect_db.inc";
mysqli_set_charset($conn, "utf8mb4");

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function normalize_text($value) {
    $value = mb_strtolower(trim((string)$value), 'UTF-8');
    $from = ['à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ','è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ','ì','í','ị','ỉ','ĩ','ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ','ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ','ỳ','ý','ỵ','ỷ','ỹ','đ'];
    $to   = ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y','d'];
    return str_replace($from, $to, $value);
}

function table_exists($conn, $table) {
    $table = mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    return $result && mysqli_num_rows($result) > 0;
}

function img($path) {
    $project = '/Bai_Thi_Cuoi_Ki/';
    $fallback = $project . "Admin/ckeditor/plugins/image/images/noimage.png";
    if (!$path) return $fallback;

    $path = trim((string)$path);
    if (str_starts_with($path, 'http')) return $path;

    $path = str_replace(['\\r\\n', '\\n', '\\r', "\r", "\n", ';', '|'], ',', $path);
    $path = str_replace('\\', '/', $path);
    $images = preg_split('/,+/', $path, -1, PREG_SPLIT_NO_EMPTY);
    if (!$images) $images = preg_split('/\s+(?=(?:img|images|uploads)\/)/i', $path, -1, PREG_SPLIT_NO_EMPTY);

    foreach ($images as $image) {
        $image = trim($image, " \t\n\r\0\x0B'\"");
        if ($image === '') continue;
        if (str_starts_with($image, $project)) $image = substr($image, strlen($project));
        $image = ltrim($image, '/');

        $candidates = [$image];
        $fileName = basename($image);
        if (!str_starts_with($image, 'uploads/')) $candidates[] = 'uploads/' . $image;
        if (!str_starts_with($image, 'img/')) $candidates[] = 'img/' . $image;
        $candidates[] = 'uploads/' . $fileName;
        $candidates[] = 'img/' . $fileName;

        foreach (array_unique($candidates) as $candidate) {
            $candidate = ltrim($candidate, '/');
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $project . $candidate)) {
                return $project . $candidate;
            }

            $dir = dirname($candidate);
            $name = pathinfo($candidate, PATHINFO_FILENAME);
            if ($dir && $name) {
                foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                    $sameName = ($dir === '.' ? '' : $dir . '/') . $name . '.' . $ext;
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $project . $sameName)) {
                        return $project . $sameName;
                    }
                }
            }
        }
    }

    return $fallback;
}

function short_text($text, $limit = 115) {
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string)$text)));
    return $text !== '' ? mb_substr($text, 0, $limit, 'UTF-8') . (mb_strlen($text, 'UTF-8') > $limit ? '...' : '') : 'Shop đang cập nhật mô tả sản phẩm.';
}

function display_price($price) {
    $price = (float)$price;
    if ($price > 0 && $price < 10000) $price *= 1000;
    return number_format($price, 0, ',', '.') . 'đ';
}

function price_value($price) {
    $price = (float)$price;
    return ($price > 0 && $price < 10000) ? $price * 1000 : $price;
}

function parse_budget($msg) {
    if (preg_match('/(?:duoi|dưới|tam|tầm|khoang|khoảng|ngan sach|ngân sách)?\s*(\d+(?:[.,]\d+)?)\s*(trieu|triệu|tr|m|k|nghin|nghìn|đ|d)?/iu', $msg, $m)) {
        $number = (float)str_replace(',', '.', $m[1]);
        $unit = normalize_text($m[2] ?? '');
        if (in_array($unit, ['trieu', 'tr', 'm'], true)) return $number * 1000000;
        if (in_array($unit, ['k', 'nghin'], true)) return $number * 1000;
        if ($number < 1000) return $number * 1000;
        return $number;
    }
    return null;
}

function has_any($msg, $words) {
    $plain = normalize_text($msg);
    foreach ($words as $word) {
        if (str_contains($plain, normalize_text($word))) return true;
    }
    return false;
}

function requested_product_types($msg) {
    $plain = normalize_text($msg);
    $types = [];

    if (preg_match('/\b(ao|shirt|polo|hoodie|jacket|khoac|thun|so mi)\b/u', $plain)) $types[] = 'ao';
    if (preg_match('/\b(quan|jean|jogger|chinos|tay|short)\b/u', $plain)) $types[] = 'quan';
    if (preg_match('/\b(giay|sneaker|loafer|boot)\b/u', $plain)) $types[] = 'giay';
    if (preg_match('/\b(dep|sandal)\b/u', $plain)) $types[] = 'dep';
    if (preg_match('/\b(phu kien|that lung|vi|non|mu|kinh|tui)\b/u', $plain)) $types[] = 'phu_kien';

    return array_values(array_unique($types));
}

function requested_product_terms($msg) {
    $plain = normalize_text($msg);
    $terms = [];

    $map = [
        'so mi' => ['so mi', 'somi', 'shirt'],
        'thun' => ['ao thun', 'thun', 't-shirt', 'tshirt'],
        'polo' => ['polo'],
        'hoodie' => ['hoodie'],
        'jacket' => ['jacket', 'ao khoac', 'khoac'],
        'len' => ['ao len', 'len'],
        'jean' => ['quan jean', 'jean', 'jeans'],
        'jogger' => ['jogger'],
        'chinos' => ['chinos'],
        'tay' => ['quan tay'],
        'short' => ['short', 'quan short'],
        'sneaker' => ['sneaker'],
        'giay tay' => ['giay tay', 'loafer'],
        'dep' => ['dep', 'sandal']
    ];

    foreach ($map as $term => $aliases) {
        foreach ($aliases as $alias) {
            if (preg_match('/(^|[^a-z0-9])' . preg_quote($alias, '/') . '([^a-z0-9]|$)/u', $plain)) {
                $terms[] = $term;
                break;
            }
        }
    }

    return array_values(array_unique($terms));
}

function requested_feature_terms($msg) {
    $plain = normalize_text($msg);
    $features = [];

    $map = [
        'caro' => ['caro', 'plaid'],
        'denim' => ['denim'],
        'linen' => ['linen', 'dui', 'đũi'],
        'basic' => ['basic', 'tron', 'trơn', 'toi gian', 'tối giản'],
        'cotton' => ['cotton'],
        'kaki' => ['kaki'],
        'jean' => ['jean', 'jeans'],
        'xanh' => ['xanh'],
        'den' => ['den', 'đen'],
        'trang' => ['trang', 'trắng'],
        'be' => ['be', 'beige'],
        'ghi' => ['ghi', 'xam', 'xám']
    ];

    foreach ($map as $feature => $aliases) {
        foreach ($aliases as $alias) {
            if (preg_match('/(^|[^a-z0-9])' . preg_quote(normalize_text($alias), '/') . '([^a-z0-9]|$)/u', $plain)) {
                $features[] = $feature;
                break;
            }
        }
    }

    return array_values(array_unique($features));
}

function product_matches_requested_terms($row, $terms) {
    if (!$terms) return true;

    $target = normalize_text(($row['name'] ?? '') . ' ' . ($row['category_name'] ?? '') . ' ' . ($row['category'] ?? ''));

    foreach ($terms as $term) {
        if (preg_match('/(^|[^a-z0-9])' . preg_quote($term, '/') . '([^a-z0-9]|$)/u', $target)) return true;
    }

    return false;
}

function product_matches_features($row, $features) {
    if (!$features) return true;

    $target = normalize_text(($row['name'] ?? '') . ' ' . ($row['category_name'] ?? '') . ' ' . ($row['category'] ?? '') . ' ' . ($row['description'] ?? ''));
    foreach ($features as $feature) {
        if (!preg_match('/(^|[^a-z0-9])' . preg_quote($feature, '/') . '([^a-z0-9]|$)/u', $target)) return false;
    }

    return true;
}

function important_search_tokens($keywordPlain) {
    $stopWords = [
        'toi','tim','mua','can','cho','voi','gia','duoi','tam','khoang','ngan','sach','hop','di','lam','hoc','choi','hen','ho','nam',
        'nay','nao','mac','phoi','do','gioi','thieu','mo','ta','chi','tiet','review','san','pham','shop','co','khong','xem','hang',
        'so','sanh','phan','van','nen','chon','va','hay','cai','mau','tren','vua','roi'
    ];
    $tokens = [];
    foreach (preg_split('/[^a-z0-9]+/u', normalize_text($keywordPlain)) as $token) {
        if (mb_strlen($token, 'UTF-8') < 2 || in_array($token, $stopWords, true)) continue;
        $tokens[] = $token;
    }
    return array_values(array_unique($tokens));
}

function clean_product_query($keywordPlain) {
    $plain = normalize_text($keywordPlain);
    $plain = preg_replace('/\b(xin|cho|toi|minh|em|anh|tim|kiem|mua|can|shop|co|khong|san pham|mau|hang|gioi thieu|mo ta|chi tiet|review|giup|nhe|nha)\b/u', ' ', $plain);
    $plain = preg_replace('/\b(duoi|tam|khoang|ngan sach)\s*\d+(?:[.,]\d+)?\s*(trieu|tr|m|k|nghin|d)?\b/u', ' ', $plain);
    return trim(preg_replace('/\s+/u', ' ', $plain));
}

function search_score($row, $keywordPlain, $requestedTerms, $requestedFeatures = []) {
    $nameCategory = normalize_text(($row['name'] ?? '') . ' ' . ($row['category_name'] ?? '') . ' ' . ($row['category'] ?? ''));
    $description = normalize_text($row['description'] ?? '');
    $nameOnly = normalize_text($row['name'] ?? '');
    $cleanQuery = clean_product_query($keywordPlain);
    $score = 0;

    if ($cleanQuery !== '' && $nameOnly === $cleanQuery) $score += 1000;
    if ($cleanQuery !== '' && str_contains($nameOnly, $cleanQuery)) $score += 260;
    if ($keywordPlain !== '' && str_contains($nameOnly, $keywordPlain)) $score += 180;
    if ($keywordPlain !== '' && str_contains($nameCategory, $keywordPlain)) $score += 120;
    foreach ($requestedTerms as $term) {
        if (str_contains($nameOnly, $term)) $score += 45;
        elseif (str_contains($nameCategory, $term)) $score += 30;
    }
    foreach ($requestedFeatures as $feature) {
        if (str_contains($nameOnly, $feature)) $score += 55;
        elseif (str_contains($nameCategory, $feature)) $score += 35;
        if (str_contains($description, $feature)) $score += 12;
    }

    foreach (important_search_tokens($keywordPlain) as $part) {
        if (str_contains($nameOnly, $part)) $score += 22;
        elseif (str_contains($nameCategory, $part)) $score += 14;
        elseif (str_contains($description, $part)) $score += 3;
    }

    $stock = isset($row['stock']) ? (int)$row['stock'] : 0;
    if ($stock > 0) $score += 2;

    return $score;
}

function product_matches_requested_type($row, $types) {
    if (!$types) return true;

    $name = normalize_text($row['name'] ?? '');
    $category = normalize_text(($row['category_name'] ?? '') . ' ' . ($row['category'] ?? ''));
    $target = trim($name . ' ' . $category);

    $map = [
        'ao' => ['ao', 'shirt', 'polo', 'hoodie', 'jacket', 'khoac', 'thun', 'so mi'],
        'quan' => ['quan', 'jean', 'jogger', 'chinos', 'tay', 'short'],
        'giay' => ['giay', 'sneaker', 'loafer', 'boot'],
        'dep' => ['dep', 'sandal'],
        'phu_kien' => ['phu kien', 'that lung', 'vi', 'non', 'mu', 'kinh', 'tui']
    ];

    foreach ($types as $type) {
        foreach ($map[$type] ?? [] as $word) {
            if (preg_match('/(^|[^a-z0-9])' . preg_quote($word, '/') . '([^a-z0-9]|$)/u', $target)) return true;
        }
    }

    return false;
}

function product_schema($conn) {
    if (table_exists($conn, 'products')) return 'products';
    return 'sanpham';
}

function remember_product($p) {
    if (!$p || empty($p['id'])) return;
    $_SESSION['last_product_id'] = (int)$p['id'];
    $_SESSION['last_product_name'] = (string)($p['name'] ?? '');
}

function remembered_product($conn) {
    if (empty($_SESSION['last_product_id'])) return null;
    return fetch_product($conn, (int)$_SESSION['last_product_id']);
}

function all_products($conn) {
    $schema = product_schema($conn);
    if ($schema === 'products') {
        $sql = "SELECT p.id, p.name, p.price, p.description, p.category, p.image, p.stock,
                       c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category OR c.name = p.category
                ORDER BY p.id DESC";
    } else {
        $sql = "SELECT sp.MaSP AS id, sp.TenSP AS name, sp.GiaBan AS price, sp.MoTaChiTiet AS description,
                       sp.AnhDaiDien AS image, dm.TenDM AS category_name,
                       COALESCE(SUM(bt.SoLuongTon), 0) AS stock,
                       GROUP_CONCAT(DISTINCT bt.KichCo ORDER BY bt.KichCo SEPARATOR ', ') AS sizes,
                       GROUP_CONCAT(DISTINCT bt.MauSac ORDER BY bt.MauSac SEPARATOR ', ') AS colors
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON dm.MaDM = sp.MaDM
                LEFT JOIN bienthesp bt ON bt.MaSP = sp.MaSP
                GROUP BY sp.MaSP
                ORDER BY sp.MaSP DESC";
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) return [];

    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    return $items;
}

function fetch_products($conn, $keyword = '', $limit = 6, $budget = null, $strictType = true) {
    $keywordPlain = normalize_text($keyword);
    $requestedTypes = $strictType ? requested_product_types($keyword) : [];
    $requestedTerms = $strictType ? requested_product_terms($keyword) : [];
    $requestedFeatures = $strictType ? requested_feature_terms($keyword) : [];
    $tokens = important_search_tokens($keywordPlain);
    $items = [];

    foreach (all_products($conn) as $row) {
        $score = $keywordPlain === '' ? 1 : search_score($row, $keywordPlain, $requestedTerms, $requestedFeatures);

        if ($strictType && $requestedTypes && !product_matches_requested_type($row, $requestedTypes)) {
            continue;
        }

        if ($budget !== null && price_value($row['price'] ?? 0) > $budget) continue;

        if ($keywordPlain !== '') {
            $minimumScore = ($requestedTerms || $requestedFeatures || count($tokens) >= 2) ? 30 : 12;
            if ($score < $minimumScore) continue;
        }

        $row['_score'] = $score;
        $items[] = $row;
    }

    usort($items, function($a, $b) {
        $scoreCompare = ($b['_score'] ?? 0) <=> ($a['_score'] ?? 0);
        if ($scoreCompare !== 0) return $scoreCompare;
        return price_value($a['price'] ?? 0) <=> price_value($b['price'] ?? 0);
    });

    return array_slice($items, 0, $limit);
}

function fetch_product($conn, $id) {
    $schema = product_schema($conn);
    $id = (int)$id;

    if ($schema === 'products') {
        $sql = "SELECT p.id, p.name, p.price, p.description, p.category, p.image, p.stock,
                       c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category OR c.name = p.category
                WHERE p.id = $id";
    } else {
        $sql = "SELECT sp.MaSP AS id, sp.TenSP AS name, sp.GiaBan AS price, sp.MoTaChiTiet AS description,
                       sp.AnhDaiDien AS image, dm.TenDM AS category_name,
                       COALESCE(SUM(bt.SoLuongTon), 0) AS stock,
                       GROUP_CONCAT(DISTINCT bt.KichCo ORDER BY bt.KichCo SEPARATOR ', ') AS sizes,
                       GROUP_CONCAT(DISTINCT bt.MauSac ORDER BY bt.MauSac SEPARATOR ', ') AS colors
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON dm.MaDM = sp.MaDM
                LEFT JOIN bienthesp bt ON bt.MaSP = sp.MaSP
                WHERE sp.MaSP = $id
                GROUP BY sp.MaSP";
    }

    $result = mysqli_query($conn, $sql);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function render_product_card($p) {
    $id = (int)$p['id'];
    $name = e($p['name'] ?? 'Sản phẩm');
    $desc = e(short_text($p['description'] ?? ''));
    $category = e($p['category_name'] ?? $p['category'] ?? 'Thời trang nam');
    $stock = isset($p['stock']) ? (int)$p['stock'] : 0;

    return "
    <div class='bot-product'>
        <img src='" . e(img($p['image'] ?? '')) . "' alt='{$name}'>
        <div class='bot-product-info'>
            <b>{$name}</b>
            <span class='bot-tag'>{$category}</span>
            <p>{$desc}</p>
            <div class='bot-row'>
                <span class='bot-price'>" . display_price($p['price'] ?? 0) . "</span>
                <span class='bot-stock'>" . ($stock > 0 ? "Còn {$stock}" : "Liên hệ tồn kho") . "</span>
            </div>
            <div class='bot-actions'>
                <button type='button' onclick='detail($id)'>Xem chi tiết</button>
                <button type='button' onclick='cart($id)'>Thêm giỏ</button>
            </div>
        </div>
    </div>";
}

function render_product_list($products, $intro = '') {
    if (!$products) return "<div class='ai'>Em chưa tìm thấy mẫu đúng ý. Anh thử tìm theo kiểu khác như áo sơ mi, quần jean, sneaker hoặc cho em biết ngân sách để em lọc sát hơn nhé.</div>";

    remember_product($products[0]);
    $html = $intro ? "<div class='ai'>" . e($intro) . "</div>" : '';
    foreach ($products as $p) $html .= render_product_card($p);
    return $html;
}

function welcome_html() {
    return "
    <div class='ai'>
        Xin chào anh 👋 Em là trợ lý thời trang nam của shop.<br>
        Anh có thể nhắn: <b>áo sơ mi</b>, <b>quần jean dưới 300k</b>, <b>đi học mặc gì</b>, <b>phối đồ hẹn hò gu basic</b>.
    </div>
    <div class='quick-actions'>
        <button type='button' onclick=\"quickSend('áo sơ mi')\">Áo sơ mi</button>
        <button type='button' onclick=\"quickSend('quần')\">Quần</button>
        <button type='button' onclick=\"quickSend('không biết mặc gì đi chơi')\">Phối đồ</button>
    </div>";
}

function detail_html($p) {
    $desc = trim((string)($p['description'] ?? ''));
    $material = preg_match('/Chất liệu:\s*([^\r\n.]+)/iu', $desc, $m) ? $m[1] : 'Chất vải được shop chọn theo tiêu chí dễ mặc, thoải mái và bền form.';
    $fit = preg_match('/Form(?: dáng)?:\s*([^\r\n.]+)/iu', $desc, $m) ? $m[1] : (preg_match('/Thiết kế:\s*([^\r\n.]+)/iu', $desc, $m) ? $m[1] : 'Form nam dễ mặc, hợp nhiều dáng người.');
    $colors = $p['colors'] ?? (preg_match('/Màu sắc:\s*([^\r\n.]+)/iu', $desc, $m) ? $m[1] : 'Tông màu dễ phối.');
    $sizes = $p['sizes'] ?? 'Anh nhắn shop để được tư vấn size theo chiều cao/cân nặng.';
    $occasion = preg_match('/Phù hợp:\s*([^\r\n.]+)/iu', $desc, $m) ? $m[1] : (preg_match('/Ứng dụng:\s*([^\r\n.]+)/iu', $desc, $m) ? $m[1] : 'Đi học, đi chơi, dạo phố hoặc dùng hằng ngày.');

    return "
    <div class='bot-detail'>
        <h3>" . e($p['name'] ?? 'Sản phẩm') . "</h3>
        <img src='" . e(img($p['image'] ?? '')) . "' alt='" . e($p['name'] ?? 'Sản phẩm') . "'>
        <h2>" . display_price($p['price'] ?? 0) . "</h2>
        <p>" . e(short_text($desc, 220)) . "</p>
        <ul>
            <li><b>Chất liệu:</b> " . e($material) . "</li>
            <li><b>Form dáng:</b> " . e($fit) . "</li>
            <li><b>Màu sắc:</b> " . e($colors) . "</li>
            <li><b>Size:</b> " . e($sizes) . "</li>
            <li><b>Phù hợp:</b> " . e($occasion) . "</li>
        </ul>
        <div class='ai'>Mẫu này hợp với anh thích vẻ ngoài gọn gàng, nam tính và dễ phối. Anh có thể bấm thêm giỏ hoặc nhắn em dịp sử dụng để em phối nguyên outfit cho anh.</div>
    </div>";
}

function product_strength($p) {
    $text = normalize_text(($p['name'] ?? '') . ' ' . ($p['description'] ?? ''));
    if (str_contains($text, 'linen') || str_contains($text, 'dui')) return 'Thoáng mát, nhẹ người, rất hợp thời tiết nóng hoặc đi chơi ngoài trời.';
    if (str_contains($text, 'denim') || str_contains($text, 'jean')) return 'Chất nam tính, bền form, dễ tạo outfit trẻ trung và mạnh mẽ.';
    if (str_contains($text, 'cotton')) return 'Mềm, thấm hút tốt, mặc lâu vẫn thoải mái.';
    if (str_contains($text, 'caro')) return 'Có họa tiết nổi bật, dễ tạo điểm nhấn khi phối đồ.';
    if (str_contains($text, 'tay') || str_contains($text, 'chinos')) return 'Lịch sự, gọn dáng, hợp đi làm hoặc hẹn hò.';
    if (str_contains($text, 'sneaker')) return 'Dễ mang, dễ phối với nhiều kiểu quần áo, hợp dùng hằng ngày.';
    return 'Dễ mặc, dễ phối, phù hợp nhiều hoàn cảnh.';
}

function product_weakness($p) {
    $text = normalize_text(($p['name'] ?? '') . ' ' . ($p['description'] ?? ''));
    if (str_contains($text, 'linen') || str_contains($text, 'dui')) return 'Dễ nhăn hơn cotton/poly, nên hợp phong cách thoải mái hơn là quá trang trọng.';
    if (str_contains($text, 'caro')) return 'Họa tiết nổi nên cần phối quần/giày đơn giản để không bị rối.';
    if (str_contains($text, 'denim')) return 'Cảm giác cá tính hơn, không phải lựa chọn tối ưu nếu anh cần vẻ ngoài quá lịch sự.';
    if (str_contains($text, 'tay') || str_contains($text, 'chinos')) return 'Ít chất streetwear hơn jean/jogger, nên hợp gu gọn gàng hơn.';
    if (str_contains($text, 'sneaker')) return 'Không trang trọng bằng giày tây trong môi trường công sở nghiêm túc.';
    return 'Không quá nổi bật nếu anh muốn outfit thật cá tính.';
}

function product_fit_reason($p, $occasion, $style) {
    $price = display_price($p['price'] ?? 0);
    $reason = "Hợp {$occasion} vì " . mb_strtolower(product_strength($p), 'UTF-8');
    return "{$reason} Giá {$price}, phù hợp {$style} và dễ cân đối túi tiền.";
}

function render_advice_card($p, $occasion, $style) {
    return render_product_card($p) . "
    <div class='bot-advice'>
        <b>Điểm mạnh:</b> " . e(product_strength($p)) . "<br>
        <b>Vì sao nên chọn:</b> " . e(product_fit_reason($p, $occasion, $style)) . "
    </div>";
}

function is_pairing_request($msg) {
    return has_any($msg, [
        'mặc hợp với', 'mac hop voi', 'phối với', 'phoi voi', 'đi với', 'di voi',
        'hợp với quần nào', 'hop voi quan nao', 'mặc với quần nào', 'mac voi quan nao',
        'phối đồ cho', 'phoi do cho', 'phối cho', 'phoi cho', 'mặc với gì', 'mac voi gi',
        'mẫu này', 'mau nay', 'sản phẩm này', 'san pham nay', 'ở trên', 'o tren', 'vừa rồi', 'vua roi'
    ]) && has_any($msg, ['quần', 'quan', 'giày', 'giay', 'dép', 'dep', 'áo', 'ao', 'mặc', 'mac', 'phối', 'phoi']);
}

function target_pair_type($msg) {
    $plain = normalize_text($msg);
    if (preg_match('/quan nao|quần nào|voi quan|với quần|\bquan\b/u', $plain)) return 'quần';
    if (preg_match('/giay nao|giày nào|voi giay|với giày|\bgiay\b|sneaker/u', $plain)) return 'giày sneaker';
    if (preg_match('/dep nao|dép nào|voi dep|với dép|\bdep\b|sandal/u', $plain)) return 'dép';
    if (preg_match('/ao nao|áo nào|voi ao|với áo|\bao\b/u', $plain)) return 'áo';
    return 'quần';
}

function anchor_query_from_pairing($msg) {
    $plain = trim((string)$msg);
    $parts = preg_split('/mặc hợp với|mac hop voi|phối với|phoi voi|đi với|di voi|mặc với|mac voi|hợp với|hop voi/iu', $plain);
    $anchor = trim($parts[0] ?? $plain);
    $anchor = preg_replace('/\b(này|nay|mẫu này|mau nay|cái này|cai nay|sản phẩm này|san pham nay|ở trên|o tren|vừa rồi|vua roi|đó|do)\b/iu', ' ', $anchor);
    return trim(preg_replace('/\s+/u', ' ', $anchor));
}

function refers_to_previous_product($msg) {
    return has_any($msg, ['mẫu này', 'mau nay', 'sản phẩm này', 'san pham nay', 'cái này', 'cai nay', 'mẫu trên', 'mau tren', 'ở trên', 'o tren', 'vừa rồi', 'vua roi', 'đó', 'do']);
}

function pairing_reason($anchor, $item, $targetType) {
    $anchorName = $anchor['name'] ?? 'mẫu anh chọn';
    $text = normalize_text(($anchor['name'] ?? '') . ' ' . ($anchor['description'] ?? '') . ' ' . ($item['name'] ?? '') . ' ' . ($item['description'] ?? ''));

    if (str_contains($text, 'caro')) {
        return "{$targetType} này hợp với {$anchorName} vì áo caro đã có họa tiết nổi, phối cùng quần/giày tông đơn giản sẽ nhìn gọn và không bị rối.";
    }
    if (str_contains($text, 'denim')) {
        return "{$targetType} này hợp với {$anchorName} vì tạo phong cách nam tính, trẻ trung, dễ mặc đi chơi hoặc dạo phố.";
    }
    if (str_contains($text, 'linen') || str_contains($text, 'dui')) {
        return "{$targetType} này hợp với {$anchorName} vì giữ tổng thể nhẹ, thoáng và thoải mái.";
    }

    return "{$targetType} này hợp với {$anchorName} vì màu/form dễ cân bằng outfit, mặc hằng ngày vẫn gọn gàng.";
}

function pairing_reply($conn, $msg) {
    $budget = parse_budget($msg);
    $targetType = target_pair_type($msg);
    $anchorQuery = anchor_query_from_pairing($msg);
    $anchor = refers_to_previous_product($msg) ? remembered_product($conn) : null;
    if (!$anchor && $anchorQuery !== '') {
        $anchorProducts = fetch_products($conn, $anchorQuery, 1, null);
        $anchor = $anchorProducts[0] ?? null;
    }

    $products = fetch_products($conn, $targetType, 3, $budget);
    if (!$products && $budget) $products = fetch_products($conn, $targetType, 3, null);

    if (!$products) {
        return "<div class='ai'>Em chưa tìm thấy {$targetType} phù hợp trong dữ liệu shop. Anh thử hỏi theo loại khác như giày hoặc quần jean nhé.</div>";
    }

    $html = "<div class='ai'>";
    if ($anchor) {
        $html .= "Em hiểu là anh muốn phối <b>" . e($anchor['name'] ?? 'sản phẩm này') . "</b> với <b>" . e($targetType) . "</b>. Em chỉ lọc đúng nhóm " . e($targetType) . " phù hợp cho anh nhé.";
    } else {
        $html .= "Em sẽ lọc đúng nhóm <b>" . e($targetType) . "</b> để phối với sản phẩm anh nhắc tới. Anh có thể gửi đúng tên mẫu nếu muốn em chốt sát hơn.";
    }
    $html .= $budget ? "<br>Em ưu tiên trong khoảng " . display_price($budget) . "." : "";
    $html .= "</div>";

    foreach ($products as $p) {
        $html .= render_product_card($p);
        $html .= "<div class='bot-advice'><b>Vì sao hợp:</b> " . e($anchor ? pairing_reason($anchor, $p, $targetType) : product_fit_reason($p, 'phối đồ', 'gu của anh')) . "<br><b>Điểm mạnh:</b> " . e(product_strength($p)) . "</div>";
    }

    return $html;
}

function introduce_reply($conn, $msg) {
    $budget = parse_budget($msg);
    $products = fetch_products($conn, $msg, 1, $budget);
    if (!$products) $products = fetch_products($conn, $msg, 1, null);

    if (!$products) {
        return "<div class='ai'>Em chưa tìm thấy đúng sản phẩm anh muốn giới thiệu. Anh nhắn rõ hơn tên mẫu giúp em, ví dụ: giới thiệu áo sơ mi denim hoặc mô tả quần jean xanh nhé.</div>";
    }

    $p = $products[0];
    remember_product($p);
    return "<div class='ai'>Dạ, em giới thiệu đúng mẫu <b>" . e($p['name'] ?? 'sản phẩm này') . "</b> cho anh nhé. Đây là mẫu phù hợp nhất với yêu cầu anh vừa nhắn.</div>"
        . detail_html($p)
        . "<div class='bot-advice'><b>Điểm mạnh nổi bật:</b> " . e(product_strength($p)) . "<br><b>Lưu ý khi chọn:</b> " . e(product_weakness($p)) . "</div>";
}

function should_show_single_product($msg, $products) {
    if (!$products) return false;
    if (has_any($msg, ['giới thiệu', 'gioi thieu', 'mô tả', 'mo ta', 'chi tiết', 'chi tiet', 'review'])) return true;

    $topScore = (int)($products[0]['_score'] ?? 0);
    $secondScore = (int)($products[1]['_score'] ?? 0);
    $tokens = important_search_tokens(normalize_text($msg));
    $cleanQuery = clean_product_query($msg);
    $topName = normalize_text($products[0]['name'] ?? '');

    if ($cleanQuery !== '' && $topName === $cleanQuery) return true;

    return $topScore >= 95 && (count($products) === 1 || $topScore >= $secondScore + 35 || count($tokens) >= 3);
}

function single_product_reply($p) {
    remember_product($p);
    return "<div class='ai'>Em tìm thấy đúng mẫu <b>" . e($p['name'] ?? 'sản phẩm này') . "</b> theo yêu cầu của anh.</div>"
        . detail_html($p)
        . "<div class='bot-advice'><b>Gợi ý nhanh:</b> Anh có thể hỏi tiếp “mẫu này phối với quần nào” hoặc “mẫu này hợp đi đâu” để em tư vấn outfit sát hơn.</div>";
}

function outfit_reply($conn, $msg) {
    $occasion = has_any($msg, ['hẹn hò', 'date']) ? 'hẹn hò' : (has_any($msg, ['đi làm', 'công sở']) ? 'đi làm' : (has_any($msg, ['đi học']) ? 'đi học' : 'đi chơi'));
    $style = has_any($msg, ['hàn quốc', 'han quoc']) ? 'gu Hàn Quốc, gọn và trẻ' : (has_any($msg, ['năng động', 'street', 'cá tính']) ? 'gu năng động, cá tính' : (has_any($msg, ['đơn giản', 'basic', 'tối giản']) ? 'gu basic, dễ mặc' : 'gu dễ mặc, nam tính'));
    $budget = parse_budget($msg);
    $sets = [
        'đi học' => 'Đi học: áo thun hoặc sơ mi basic + quần jean xanh + sneaker trắng. Set này trẻ trung, sạch sẽ và không bị quá cầu kỳ.',
        'đi chơi' => 'Đi chơi: sơ mi form rộng khoác ngoài áo thun + quần jean hoặc short + sneaker/sandal. Vừa thoải mái vừa có điểm nhấn.',
        'hẹn hò' => 'Hẹn hò: áo polo hoặc sơ mi trơn + quần tây/chinos + giày lười hoặc sneaker trắng. Nhìn lịch sự, gọn gàng và dễ tạo thiện cảm.',
        'đi làm' => 'Đi làm: sơ mi trơn + quần tây/chinos + giày tây hoặc sneaker tối giản. Ưu tiên màu navy, đen, trắng, ghi để dễ phối.'
    ];

    $topBudget = $budget ?: null;
    $shirts = fetch_products($conn, "áo {$occasion} {$style}", 2, $topBudget);
    $pants = fetch_products($conn, "quần {$occasion} {$style}", 2, $topBudget);
    $shoes = fetch_products($conn, "giày sneaker dép {$occasion} {$style}", 2, $topBudget);

    $html = "<div class='ai'>✨ " . e($sets[$occasion]) . "<br>Em phối theo " . e($style) . ($budget ? ", ưu tiên món hợp túi tiền khoảng " . display_price($budget) : "") . ".</div>";
    if ($shirts) {
        $html .= "<div class='ai'><b>Áo nên chọn</b></div>";
        foreach ($shirts as $p) $html .= render_advice_card($p, $occasion, $style);
    }
    if ($pants) {
        $html .= "<div class='ai'><b>Quần phối cùng</b></div>";
        foreach ($pants as $p) $html .= render_advice_card($p, $occasion, $style);
    }
    if ($shoes) {
        $html .= "<div class='ai'><b>Giày/dép hoàn thiện outfit</b></div>";
        foreach ($shoes as $p) $html .= render_advice_card($p, $occasion, $style);
    }

    if (!$shirts && !$pants && !$shoes) {
        $html .= render_product_list(fetch_products($conn, '', 5, $topBudget), "Em chưa bắt đúng món theo outfit này, nhưng có vài sản phẩm giá hợp lý để anh tham khảo trước.");
    }

    return $html;
}

function advice_reply($conn, $msg) {
    $budget = parse_budget($msg);
    $style = has_any($msg, ['hàn quốc', 'han quoc']) ? 'Hàn Quốc' : (has_any($msg, ['năng động', 'street', 'cá tính']) ? 'năng động' : (has_any($msg, ['đơn giản', 'basic', 'tối giản']) ? 'basic' : 'dễ mặc'));
    $purpose = has_any($msg, ['đi làm', 'công sở']) ? 'đi làm' : (has_any($msg, ['hẹn hò']) ? 'hẹn hò' : (has_any($msg, ['đi học']) ? 'đi học' : 'đi chơi'));
    $query = trim($purpose . ' ' . $style . ' ' . $msg);
    $products = fetch_products($conn, $query, 5, $budget);
    if (!$products) $products = fetch_products($conn, $msg, 5, $budget);
    if (!$products) $products = fetch_products($conn, '', 5, $budget);

    $budgetText = $budget ? ' trong ngân sách khoảng ' . display_price($budget) : '';
    return render_product_list($products, "Em gợi ý các món hợp phong cách {$style}, dùng để {$purpose}{$budgetText}. Các mẫu này dễ phối, giá rõ ràng và phù hợp thời trang nam hằng ngày.");
}

function compare_reply($conn, $msg) {
    $budget = parse_budget($msg);
    $purpose = has_any($msg, ['đi làm', 'công sở']) ? 'đi làm' : (has_any($msg, ['hẹn hò']) ? 'hẹn hò' : (has_any($msg, ['đi học']) ? 'đi học' : 'đi chơi'));
    $products = fetch_products($conn, $msg, 3, $budget);
    if (count($products) < 2) $products = fetch_products($conn, $msg, 3, null);
    if (count($products) < 2) {
        $types = requested_product_types($msg);
        $typeWords = ['ao' => 'áo', 'quan' => 'quần', 'giay' => 'giày', 'dep' => 'dép', 'phu_kien' => 'phụ kiện'];
        $fallback = trim(implode(' ', array_map(fn($type) => $typeWords[$type] ?? '', $types)));
        if ($fallback !== '') $products = fetch_products($conn, $fallback, 3, $budget);
    }
    if (count($products) < 2) return "<div class='ai'>Em chưa có đủ sản phẩm để so sánh chính xác. Anh gửi giúp em tên 2 mẫu đang phân vân, ví dụ: so sánh áo sơ mi denim và áo sơ mi linen nhé.</div>";

    $best = null;
    foreach ($products as $p) {
        if ($budget && price_value($p['price'] ?? 0) > $budget) continue;
        if (!$best || ($p['_score'] ?? 0) > ($best['_score'] ?? 0) || price_value($p['price'] ?? 0) < price_value($best['price'] ?? 0)) $best = $p;
    }
    if (!$best) $best = $products[0];

    $html = "<div class='ai'>Anh đang phân vân thì em so sánh theo <b>giá</b>, <b>điểm mạnh/yếu</b> và mức hợp với nhu cầu <b>" . e($purpose) . "</b> nhé:</div><div class='bot-compare'>";
    foreach ($products as $p) {
        $name = e($p['name'] ?? 'Sản phẩm');
        $category = e($p['category_name'] ?? $p['category'] ?? 'Thời trang nam');
        $inBudget = !$budget || price_value($p['price'] ?? 0) <= $budget;
        $html .= "
            <div class='bot-compare-item'>
                <b>{$name}</b>
                <span>{$category}</span>
                <strong>" . display_price($p['price'] ?? 0) . "</strong>
                <p><b>Điểm mạnh:</b> " . e(product_strength($p)) . "</p>
                <p><b>Điểm yếu:</b> " . e(product_weakness($p)) . "</p>
                <p><b>Phù hợp:</b> " . e(product_fit_reason($p, $purpose, 'gu của anh')) . "</p>
                <p><b>Túi tiền:</b> " . ($inBudget ? "Nằm trong ngân sách." : "Vượt ngân sách, chỉ nên chọn nếu anh thật sự thích mẫu này.") . "</p>
            </div>";
    }
    $html .= "</div><div class='ai'>Kết luận: em nghiêng về <b>" . e($best['name'] ?? 'mẫu đầu tiên') . "</b> vì cân bằng tốt giữa giá, độ dễ phối và nhu cầu " . e($purpose) . ". " . ($budget ? "Mẫu này cũng hợp ngân sách khoảng " . display_price($budget) . " hơn." : "Nếu anh cho em thêm ngân sách, em sẽ chốt sát túi tiền hơn.") . "</div>";
    return $html;
}

if (isset($_POST['msg'])) {
    $rawMsg = trim((string)$_POST['msg']);
    $msg = normalize_text($rawMsg);

    if ($msg === '') {
        echo "<div class='ai'>Anh nhập giúp em sản phẩm hoặc nhu cầu nhé, ví dụ: áo sơ mi đi làm dưới 300k, quần jean, không biết mặc gì.</div>";
        exit;
    }

    if (in_array($msg, ['hi', 'hello', 'xin chao', 'chao', 'hey'], true)) {
        echo welcome_html();
        exit;
    }

    if (is_pairing_request($rawMsg)) {
        echo pairing_reply($conn, $rawMsg);
        exit;
    }

    if (has_any($rawMsg, ['giới thiệu', 'gioi thieu', 'mô tả', 'mo ta', 'chi tiết', 'chi tiet', 'review sản phẩm', 'review san pham'])) {
        echo introduce_reply($conn, $rawMsg);
        exit;
    }

    if (!requested_product_types($rawMsg) && has_any($rawMsg, ['danh sách sản phẩm', 'danh sach san pham', 'sản phẩm', 'san pham', 'shop có gì', 'shop co gi', 'xem hàng', 'xem hang'])) {
        echo render_product_list(fetch_products($conn, '', 8), "Dạ đây là một số sản phẩm đang có trong shop. Anh thích áo, quần, giày hay cần em lọc theo ngân sách ạ?");
        exit;
    }

    if (has_any($rawMsg, ['không biết mặc gì', 'khong biet mac gi', 'outfit', 'phối đồ', 'phoi do', 'mặc gì', 'mac gi'])) {
        echo outfit_reply($conn, $rawMsg);
        exit;
    }

    if (has_any($rawMsg, ['so sánh', 'so sanh', 'phân vân', 'phan van', 'nên chọn', 'nen chon'])) {
        echo compare_reply($conn, $rawMsg);
        exit;
    }

    if (has_any($rawMsg, ['tư vấn', 'tu van', 'gợi ý', 'goi y', 'ngân sách', 'ngan sach', 'đi học', 'đi chơi', 'hẹn hò', 'đi làm', 'basic', 'hàn quốc', 'năng động'])) {
        echo advice_reply($conn, $rawMsg);
        exit;
    }

    $budget = parse_budget($rawMsg);
    $products = fetch_products($conn, $rawMsg, 6, $budget);
    if (should_show_single_product($rawMsg, $products)) {
        echo single_product_reply($products[0]);
        exit;
    }
    echo render_product_list($products, $products ? "Em tìm thấy vài mẫu hợp yêu cầu của anh. Giá bên dưới đã hiển thị rõ để anh dễ so sánh nhé." : '');
    exit;
}

if (isset($_POST['detail'])) {
    $p = fetch_product($conn, (int)$_POST['detail']);
    if ($p) remember_product($p);
    echo $p ? detail_html($p) : "<div class='ai'>Em chưa tìm thấy sản phẩm này, anh thử chọn mẫu khác giúp em nhé.</div>";
    exit;
}

if (isset($_POST['cart'])) {

    $id = (int)$_POST['cart'];

    // Lấy thông tin sản phẩm
    $product = fetch_product($conn, $id);

    // Không tìm thấy sản phẩm
    if (!$product) {

        echo "ERROR";
        exit;
    }

    // Khởi tạo giỏ hàng
    if (!isset($_SESSION['cart'])) {

        $_SESSION['cart'] = [];
    }

    // Key sản phẩm
    $key = $id . "_default";

    // Nếu đã có sản phẩm
    if (isset($_SESSION['cart'][$key])) {

        $_SESSION['cart'][$key]['soluong']++;

    } else {

        // Size mặc định
        $defaultSize = 'M';

        if (!empty($product['sizes'])) {

            $sizes = explode(',', $product['sizes']);

            $defaultSize = trim($sizes[0]);
        }

        // Màu mặc định
        $defaultColor = 'Mặc định';

        if (!empty($product['colors'])) {

            $colors = explode(',', $product['colors']);

            $defaultColor = trim($colors[0]);
        }

        // Thêm vào session cart đúng cấu trúc
        $_SESSION['cart'][$key] = [

            'id' => $id,

            'ten' => $product['name'] ?? 'Sản phẩm',

            'gia' => price_value($product['price'] ?? 0),

            'anh' => img($product['image'] ?? ''),

            'soluong' => 1,

            'kichco' => $defaultSize,

            'mausac' => $defaultColor
        ];
    }

    echo "OK";

    exit;
}
?>

<style>
body{font-family:Arial,sans-serif}
#openBtn{position:fixed;right:20px;bottom:20px;background:#111827;color:#fff;width:54px;height:54px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 8px 24px rgba(0,0,0,.28);z-index:9998}
#chatbot{position:fixed;right:20px;bottom:86px;width:390px;height:560px;background:#fff;border-radius:12px;box-shadow:0 14px 36px rgba(0,0,0,.24);display:none;flex-direction:column;overflow:hidden;z-index:9999;border:1px solid #e5e7eb}
#header{background:#111827;color:#fff;padding:13px 14px;display:flex;justify-content:space-between;align-items:center;font-weight:700}
#header small{display:block;color:#d1d5db;font-weight:400;margin-top:2px}
#chat{flex:1;overflow:auto;padding:12px;background:#f3f4f6}
#input{display:flex;border-top:1px solid #e5e7eb;background:#fff}
#input input{flex:1;padding:12px;border:none;outline:none;font-size:14px}
#input button{background:#111827;color:#fff;border:none;padding:0 16px;cursor:pointer;font-weight:700}
.user-msg{background:#dbeafe;color:#111827;padding:8px 10px;border-radius:10px;margin:7px 0 7px auto;max-width:86%;width:max-content}
.ai{background:#fff7ed;color:#1f2937;padding:9px 10px;border-radius:10px;margin:7px 0;border:1px solid #fed7aa;line-height:1.45}
.bot-product{display:flex;gap:10px;background:#fff;border-radius:10px;padding:10px;margin:9px 0;border:1px solid #e5e7eb}
.bot-product img{width:78px;height:88px;object-fit:cover;border-radius:8px;background:#f3f4f6}
.bot-product-info{flex:1;min-width:0}
.bot-product-info b{display:block;color:#111827;line-height:1.25}
.bot-product-info p{font-size:13px;color:#4b5563;margin:6px 0;line-height:1.35}
.bot-tag{display:inline-block;font-size:11px;background:#eef2ff;color:#3730a3;border-radius:999px;padding:2px 7px;margin-top:5px}
.bot-row{display:flex;justify-content:space-between;gap:8px;align-items:center}
.bot-price{color:#dc2626;font-weight:800}
.bot-stock{font-size:12px;color:#047857}
.bot-actions{display:flex;gap:6px;margin-top:8px}
.bot-actions button{border:none;padding:7px 9px;border-radius:7px;font-size:12px;cursor:pointer}
.bot-actions button:first-child{background:#2563eb;color:#fff}
.bot-actions button:last-child{background:#16a34a;color:#fff}
.bot-detail{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:12px;margin:9px 0}
.bot-detail h3{margin:0 0 8px;color:#111827}
.bot-detail img{width:100%;max-height:240px;object-fit:cover;border-radius:9px;background:#f3f4f6}
.bot-detail h2{color:#dc2626;margin:10px 0 6px}
.bot-detail p,.bot-detail li{font-size:14px;color:#374151;line-height:1.45}
.bot-detail ul{padding-left:18px;margin:8px 0}
.bot-advice{background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;color:#334155;font-size:13px;line-height:1.45;margin:-4px 0 10px;padding:9px 10px}
.bot-compare{display:grid;grid-template-columns:1fr;gap:8px;margin:9px 0}
.bot-compare-item{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:10px}
.bot-compare-item b{display:block;color:#111827}
.bot-compare-item span{display:inline-block;color:#4f46e5;background:#eef2ff;border-radius:999px;font-size:11px;padding:2px 7px;margin:5px 0}
.bot-compare-item strong{display:block;color:#dc2626;margin-bottom:4px}
.bot-compare-item p{margin:0;color:#4b5563;font-size:13px;line-height:1.35}
.quick-actions{display:flex;flex-wrap:wrap;gap:7px;margin:8px 0}
.quick-actions button{border:1px solid #d1d5db;background:#fff;color:#111827;border-radius:999px;padding:7px 10px;cursor:pointer;font-size:12px}
.quick-actions button:hover{border-color:#111827}
@media(max-width:480px){#chatbot{right:10px;left:10px;width:auto;height:72vh}#openBtn{right:14px;bottom:14px}}
</style>

<div id="openBtn" onclick="toggle()">💬</div>

<div id="chatbot">
    <div id="header">
        <span>Trợ lý thời trang nam<small>Tư vấn sản phẩm, giá và outfit</small></span>
        <span onclick="toggle()" style="cursor:pointer">×</span>
    </div>

    <div id="chat">
        <?php echo welcome_html(); ?>
    </div>

    <div id="input">
        <input id="msg" placeholder="Ví dụ: áo sơ mi đi làm dưới 300k..." autocomplete="off">
        <button onclick="send()">Gửi</button>
    </div>
</div>

<script>
let open = false;
const CHATBOT_ENDPOINT = "/Bai_Thi_Cuoi_Ki/chatbot.php";

function toggle(){
    open = !open;
    document.getElementById("chatbot").style.display = open ? "flex" : "none";
}

document.addEventListener("keydown", function(e){
    if(e.key === "Enter" && document.activeElement.id === "msg") send();
});

function add(html){
    const d = document.createElement("div");
    d.innerHTML = html;
    document.getElementById("chat").appendChild(d);
    d.scrollIntoView({behavior:"smooth", block:"end"});
}

function escapeHtml(text){
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

function send(){
    const m = document.getElementById("msg");
    const value = m.value.trim();
    if(!value) return;

    add("<div class='user-msg'>" + escapeHtml(value) + "</div>");
    m.value = "";

    fetch(CHATBOT_ENDPOINT,{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"msg=" + encodeURIComponent(value)
    })
    .then(r => r.text())
    .then(d => add(d))
    .catch(() => add("<div class='ai'>Em đang gặp lỗi kết nối. Anh thử lại giúp em sau ít phút nhé.</div>"));
}

function quickSend(text){
    const m = document.getElementById("msg");
    m.value = text;
    send();
}

function detail(id){
    fetch(CHATBOT_ENDPOINT,{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"detail=" + encodeURIComponent(id)
    }).then(r => r.text()).then(d => add(d));
}

function cart(id){
    fetch(CHATBOT_ENDPOINT,{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"cart=" + encodeURIComponent(id)
    }).then(() => add("<div class='ai'>Đã thêm vào giỏ hàng rồi anh nhé 🛒 Anh muốn em phối thêm áo/quần đi kèm không?</div>"));
}
</script>
