<?php
session_start();
require_once "backend/database.php";

$userLoggedIn = isset($_SESSION['user_id']);
$userName     = $userLoggedIn ? $_SESSION['username'] : null;

// fetch products (use your existing table)
$pdo = getPDO();
$stmt = $pdo->query("SELECT * FROM products WHERE status='active' ORDER BY id DESC LIMIT 24");
$products = $stmt->fetchAll();

// hero images list (ONLY images you already have in /images/)
$heroImages = [
  'cabbage1.jpg',
  'tomato2.png',
  'banana3.jpg',
  'mango1.jpg',
  'pineapple1.jpg',
  'beans2.jpg',
  'avocado1.jpg',
  'tomato1.webp',
  'maize0.jpg',
  'garlic.jpg',
  'onion1.jpg',
  'orange1.jpg',
  'tomato1.webp',
  'potato1.png'
];

// popular categories (you can change these or load from DB)
$categories = [
  ['name'=>'Vegetables','img'=>'tomato2.png'],
  ['name'=>'Fruits','img'=>'banana3.jpg'],
  ['name'=>'Roots','img'=>'potato1.png'],
  ['name'=>'Grains','img'=>'beans1.jpg'],
  ['name'=>'Herbs','img'=>'lemon1.jpg'],
  ['name'=>'Dairy','img'=>'apple1.jpg'] // substitute if no dairy image
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>AgriSmart — Fresh from Farmers</title>

<style>
<style>
/* Floating FAB Button */
.dark-toggle {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #1b5e20;
  color: white;
  width: 55px;
  height: 55px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 26px;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(0,0,0,0.28);
  z-index: 99999;
  transition: 0.3s;
}
.dark-toggle:hover {
  transform: scale(1.14);
  background: #145a17;
}

/* DARK MODE GLOBAL */
.dark {
  background:#0e1712 !important;
  color:#e3e9e4 !important;
}

/* NAVBAR DARK */
.dark .nav,
.dark header,
.dark .topbar {
  background:#111 !important;
  color:#fff !important;
  box-shadow:none !important;
}

/* CARD DARK */
.dark .card,
.dark .product,
.dark .cat-card,
.dark .container,
.dark table,
.dark .section,
.dark .wrap {
  background:#1b1f1d !important;
  color:#eee !important;
  box-shadow:none !important;
}

/* TEXT */
.dark h1, 
.dark h2,
.dark h3,
.dark h4,
.dark a {
  color:#d2d8d0 !important;
}

/* INPUTS & SELECT */
.dark input,
.dark select,
.dark textarea {
  background:#222 !important;
  color:#fff !important;
  border:1px solid #444 !important;
}

/* SIDEBAR */
.dark .sidebar {
  background:#0d0f0e !important;
}

/* TABLE */
.dark table th {
  background:#222 !important;
  color:#fff !important;
}
.dark table td {
  border-bottom:1px solid #333 !important;
}

/* PRODUCTS IMAGES BORDER */
.dark img {
  border-color:#333 !important;
}

/* HERO SECTION */
.dark .hero {
  background: linear-gradient(90deg, #0a2910, #1b5e20) !important;
}
body {
    background: var(--bg);
    color: var(--text);
}
 
:root{
  --green: #1b5e20;
  --green-2:#2e7d32;
  --accent: #66bb6a;
  --muted: #65737e;
  --bg: #f4fbf7;
  --card: #ffffff;
  --shadow: 0 10px 30px rgba(8,35,13,0.06);
  font-family: Poppins, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
  --ease: cubic-bezier(.2,.9,.2,1);
}

* { box-sizing: border-box; }
html,body { height:100%; margin:0; background:var(--bg); color:#0f1720; -webkit-font-smoothing:antialiased; }

/* NAV */
.nav {
  width:100%;
  background:var(--card);
  padding:12px 28px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  box-shadow: var(--shadow);
  position:sticky; top:0; z-index:999;
}
.nav .brand { display:flex; gap:12px; align-items:center; }
.brand img{ width:46px; height:46px; border-radius:10px; object-fit:cover; }
.brand .title{ font-weight:800; color:var(--green); font-size:20px; }
.menu { display:flex; gap:12px; align-items:center; }
.menu a { text-decoration:none; color:var(--muted); font-weight:600; padding:8px 12px; border-radius:8px; transition: all .18s var(--ease); }
.menu a:hover { color:var(--green); transform:translateY(-3px); background: rgba(27,94,32,0.04); }

/* HERO */
.hero {
  display:flex;
  align-items:center;
  gap:28px;
  padding:64px 36px;
  background: linear-gradient(90deg, rgba(27,94,32,1), rgba(102,187,106,1));
  color:white;
  overflow:hidden;
}
.hero-left{ flex:1 1 540px; min-width:260px; }
.hero-left h1{ font-size:44px; margin:0 0 8px 0; line-height:1.02; font-weight:800; text-shadow: 0 6px 22px rgba(0,0,0,0.18); }
.hero-left p{ margin:0; color:rgba(255,255,255,0.92); font-size:16px; }
.hero-cta{ margin-top:20px; display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
.btn-primary {
  background:#fff;color:var(--green); padding:12px 20px; border-radius:12px; font-weight:800; border:0; cursor:pointer; box-shadow:0 8px 26px rgba(0,0,0,0.12);
  transition: transform .18s var(--ease), box-shadow .18s var(--ease);
}
.btn-primary:hover{ transform: translateY(-4px) scale(1.02); box-shadow:0 18px 36px rgba(0,0,0,0.18); }

.hero-right{ flex:0 0 420px; text-align:center; }
.hero-img {
  width:420px; max-width:92%;
  border-radius:18px;
  box-shadow:0 18px 46px rgba(0,0,0,0.25);
  transform-origin:center center;
  transition: transform .6s var(--ease);
}
.hero-img:hover { transform: translateY(-8px) scale(1.02); }

/* CATEGORIES */
.section { padding:36px 36px; max-width:1200px; margin:0 auto; }
.section h2{ color:var(--green); font-size:26px; margin:0 0 12px 0; }
.cat-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:14px; margin-top:16px; }
.cat-card{
  background:var(--card); border-radius:14px; padding:12px; display:flex; flex-direction:column; align-items:center; justify-content:center;
  box-shadow: var(--shadow); transition: transform .28s var(--ease), box-shadow .28s var(--ease);
  cursor:pointer; will-change: transform;
}
.cat-card:hover{ transform: translateY(-10px) scale(1.05); box-shadow: 0 26px 60px rgba(8,35,13,0.12); }
.cat-card img{ width:82px; height:82px; border-radius:12px; object-fit:cover; }

/* PRODUCT SLIDER */
.products-wrapper{ padding:16px 0; overflow:hidden; }
.product-row{
  display:flex; gap:16px; width:max-content;
  animation: scrollLeft 48s linear infinite;
}
@keyframes scrollLeft { from{ transform: translateX(0); } to{ transform: translateX(-100%); } }

/* PRODUCT CARD */
.product {
  width:220px; background:var(--card); border-radius:14px; padding:12px; text-align:center;
  box-shadow: var(--shadow); transition: transform .28s var(--ease), box-shadow .28s var(--ease);
  will-change: transform;
  transform: translateY(0);
  animation: floatUp 6.5s ease-in-out infinite;
}
.product:hover { transform: translateY(-12px) scale(1.03); box-shadow: 0 26px 60px rgba(8,35,13,0.14); }
@keyframes floatUp {
  0% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
  100% { transform: translateY(0); }
}
.product img { width:100%; height:140px; object-fit:cover; border-radius:12px; transition: transform .36s var(--ease); }
.product img:hover { transform: scale(1.12) rotate(-1deg); }
.product h4{ margin:10px 0 6px; color:var(--green); font-size:16px; }
.product .price { color:#0b3a19; font-weight:800; margin-top:6px; }

/* entrance animation (staggered via JS) */
@keyframes appearUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }

/* small responsive tweaks */
footer { padding:26px; text-align:center; background:#0f3614; color:white; margin-top:26px; border-top:4px solid rgba(255,255,255,0.02); }

/* mobile */
@media (max-width:830px) {
  .hero { padding:36px 18px; flex-direction:column; gap:20px; }
  .hero-right { order: -1; }
  .product { width:170px; }
  .hero-img { width:92%; }
}
</style>
</head>
<body>

<!-- NAV -->
<header class="nav">
  <div class="brand">
    <img src="images/agrismart.jpg" alt="logo">
    <div class="title">AgriSmart</div>
  </div>
<!-- DARK MODE BUTTON -->
<div id="darkModeToggle" class="dark-toggle">🌓</div>

  <nav class="menu" aria-label="Main navigation">
    <a href="index.php">Home</a>
    <a href="customer/shop.php">Shop</a>
    <a href="customer/services.php">Services</a>
    <a href="customer/promotions.php">Promotions</a>
    <a href="customer/staff.php">Staff</a>
    <a href="customer/partners.php">Partners</a>
    <a href="customer/profile.php">Profile</a>
    <a href="contact.php">Contact</a>

    <?php if($userLoggedIn): ?>
      <a style="color:var(--green);font-weight:700;">Hi, <?= htmlspecialchars($userName) ?></a>
      <a href="backend/logout.php" style="color:red;">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>


<!-- HERO -->
<section class="hero" role="banner" aria-label="Main hero">
  <div class="hero-left">
    <h1>Fresh Produce — Delivered From Trusted Farmers</h1>
    <p>Order fresh vegetables, fruits and staples directly from local farms. Fast delivery across your area.</p>

    <div class="hero-cta">
      <?php if($userLoggedIn): ?>
        <button class="btn-primary" onclick="location.href='customer/shop.php'">Shop Now</button>
      <?php else: ?>
        <button class="btn-primary" onclick="location.href='../login.php'">Login to Shop</button>
      <?php endif; ?>
      <a href="customer/shop.php" style="color:white; text-decoration:underline; align-self:center; opacity:0.95">Browse products</a>
    </div>
  </div>

  <div class="hero-right" aria-hidden="false">
    <!-- hero image will rotate via JS among the images in $heroImages -->
    <img id="heroImg" class="hero-img" src="images/<?php echo htmlspecialchars($heroImages[0]); ?>" alt="Fresh produce hero">
  </div>
</section>

<!-- CATEGORIES -->
<section class="section">
  <h2>Popular Categories</h2>
  <div class="cat-grid" role="list">
    <?php foreach($categories as $c): ?>
      <div role="listitem" class="cat-card" onclick="location.href='customer/shop.php?category=<?php echo urlencode($c['name']); ?>'">
        <img src="images/<?php echo htmlspecialchars($c['img']); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>">
        <h4><?php echo htmlspecialchars($c['name']); ?></h4>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- PRODUCTS (auto-scrolling slider) -->
<section class="section">
  <h2>Popular Products</h2>
  <div class="products-wrapper" aria-live="polite">
    <div class="product-row" id="productRow">
      <?php if (count($products) === 0): ?>
        <!-- fallback sample cards if DB empty -->
        <?php $fallback = ['tomato2.png','banana3.jpg','potato1.png','pineapple1.jpg']; ?>
        <?php foreach($fallback as $f): ?>
          <div class="product" role="article">
            <img src="images/<?php echo htmlspecialchars($f); ?>" alt="">
            <h4>Sample</h4>
            <div class="price">Frw 1,000</div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php foreach($products as $p): ?>
          <div class="product" role="article" data-id="<?php echo (int)$p['id']; ?>">
            <img src="images/<?php echo htmlspecialchars($p['image'] ?: 'placeholder.png'); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
            <h4><?php echo htmlspecialchars($p['name']); ?></h4>
            <div class="price"><?php echo number_format($p['price']); ?> Frw</div>
            <div style="margin-top:8px;">
              <form method="POST" action="backend/add_to_cart.php" style="display:inline-block">
                <input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" style="border:0;background:var(--green);color:white;padding:8px 12px;border-radius:8px;cursor:pointer;">Add to cart</button>
              </form>
              <a href="customer/product-details.php?id=<?php echo (int)$p['id']; ?>" style="margin-left:8px;text-decoration:none;color:var(--muted)">Details</a>
            </div>
          </div>
        <?php endforeach; ?>
        
        <!-- duplicate the same nodes to make seamless loop -->
        <?php foreach($products as $p): ?>
          <div class="product" role="article" aria-hidden="true">
            <img src="images/<?php echo htmlspecialchars($p['image'] ?: 'placeholder.png'); ?>" alt="">
            <h4><?php echo htmlspecialchars($p['name']); ?></h4>
            <div class="price"><?php echo number_format($p['price']); ?> Frw</div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<footer>
  © <?php echo date("Y"); ?> AgriSmart — Fresh produce from local farmers.
</footer>

<script>
/* ---------------- HERO IMAGE ROTATION ---------------- */
(function(){
  const heroImgs = <?php echo json_encode($heroImages, JSON_HEX_TAG); ?>;
  let idx = 0;
  const el = document.getElementById('heroImg');
  // rotate every 3500ms
  setInterval(() => {
    idx = (idx + 1) % heroImgs.length;
    // crossfade
    el.style.transition = 'opacity .10s ease, transform .10s ease';
    el.style.opacity = 0;
    setTimeout(()=> {
      el.src = 'images/' + heroImgs[idx];
      el.style.opacity = 1;
    }, 600);
  }, 3500);
})();

/* ---------------- STAGGERED APPEAR for products ---------------- */
document.addEventListener('DOMContentLoaded', () => {
  const cards = Array.from(document.querySelectorAll('.product'));
  cards.forEach((c, i) => {
    c.style.opacity = 0;
    c.style.animation = `appearUp .48s ease forwards`;
    c.style.animationDelay = (i * 60) + 'ms';
  });

  // make product-row pause on hover (improve UX)
  const row = document.getElementById('productRow');
  row.addEventListener('mouseenter', () => row.style.animationPlayState = 'paused');
  row.addEventListener('mouseleave', () => row.style.animationPlayState = 'running');

  // small accessibility: keyboard focus styling on product images
  document.querySelectorAll('.product img').forEach(img=>{
    img.setAttribute('tabindex','0');
    img.addEventListener('focus', ()=> img.style.transform = 'scale(1.08)');
    img.addEventListener('blur', ()=> img.style.transform = '');
  });
});

/* ---------------- touch-friendly improvement: allow horizontal swipe to pause autoplay ---------------- */
let touchStartX = null;
document.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; });
document.addEventListener('touchmove', (e) => {
  if (!touchStartX) return;
  const dx = e.touches[0].clientX - touchStartX;
  if (Math.abs(dx) > 20) {
    // pause the auto-scrolling while user interacts
    document.getElementById('productRow').style.animationPlayState = 'paused';
  }
});
document.addEventListener('touchend', () => {
  document.getElementById('productRow').style.animationPlayState = 'running';
  touchStartX = null;
});
// APPLY SAVED THEME

document.addEventListener("DOMContentLoaded", () => {
    let mode = localStorage.getItem("theme");
    if (mode === "dark") {
        document.body.classList.add("dark");
    }
});

// TOGGLE BUTTON
document.getElementById("darkModeToggle").onclick = () => {
    document.body.classList.toggle("dark");

    // save mode
    if (document.body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.setItem("theme", "light");
    }
};
</script>

</body>
</html>
