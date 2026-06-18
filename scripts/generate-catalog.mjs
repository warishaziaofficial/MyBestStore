import fs from "fs";

const scraped = JSON.parse(
  fs.readFileSync("scripts/scraped-products.json", "utf8")
);

const CATEGORY_ALIASES = {
  "lp-record": "lp-records",
  homepage: "homepage",
};

const CATEGORY_META = {
  "new-arrivals": { name: "New Arrivals", subCategory: "Latest" },
  "led-tvs": { name: "LED TVs", subCategory: "Televisions" },
  "sound-bars": { name: "Sound Bars", subCategory: "Audio" },
  "air-purifiers": { name: "Air Purifiers", subCategory: "Home Appliances" },
  "home-theater": { name: "Home Theater", subCategory: "Entertainment" },
  "blu-ray-movies": { name: "Blu-ray Movies", subCategory: "Movies & Entertainment" },
  "4k-moives": { name: "4K Movies", subCategory: "Movies & Entertainment" },
  "lp-records": { name: "LP Records", subCategory: "Books & Media" },
  "audio-equipment": { name: "Audio Equipment", subCategory: "Audio" },
  "mobile-accessories": { name: "Mobile Accessories", subCategory: "Accessories" },
  "tv-trolly-stand": { name: "TV Stands", subCategory: "Accessories" },
  accessories: { name: "Accessories", subCategory: "Accessories" },
  posters: { name: "Posters", subCategory: "Entertainment" },
  dvd: { name: "DVD Movies", subCategory: "Movies & Entertainment" },
  books: { name: "Books & Media", subCategory: "Books" },
  cameras: { name: "Cameras", subCategory: "Electronics" },
  computing: { name: "Computing", subCategory: "Electronics" },
  "smart-gadgets": { name: "Smart Gadgets", subCategory: "Electronics" },
};

const CATEGORY_IMAGES = {
  "new-arrivals":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/at-vm95e-box_1000x1000_6cb5ff00-96fa-4db8-91a9-b2084ad3ff94.jpg",
  "led-tvs":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/51oKFzORCHL._AC_SL1500.jpg",
  "sound-bars":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/Alpha-VS21-BLK-1000x1000.jpg",
  "air-purifiers":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/shasha2625_101582868359.jpg",
  "home-theater":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/pioneer-htz272-code-free-home-theatre-system-for-110-240-volts-20.jpg",
  "blu-ray-movies":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/81JLnCHVlfL._AC_SX425.jpg",
  "4k-moives":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/41_W4m85vTL.jpg",
  "lp-records":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/dilwale-svr-005-cover-book-fold-red-coloured-lp-record-coming-date-till-20th-to-25th-july-1.jpg",
  "audio-equipment":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/712HtUkI_TL._AC_SX569.jpg",
  "mobile-accessories":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/BT185H-pix.jpg",
  "tv-trolly-stand":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/26-32inch-TV-Ceiling-Roof-Mount-Bracket-LCD-LED-Monitor-Holder-Flat-Tilting-Tools-Accessory-Loading.png",
  accessories:
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/High-Quality-Wireless-HDMI-Extender-50M-HD-1080P-Video-Audio-Signal-Transmission-System-HDMI-wireless-Extender.jpg",
  posters:
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/550x767.jpg",
  books:
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/border-vcf-3554-cover-book-fold-lp-record-in-stock-1.jpg",
  cameras:
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/olympusE5101.png",
  computing:
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/vx610_angled_low_1496731555.756474.jpg",
  "smart-gadgets":
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/DA6211-RGB.jpg",
  dvd:
    "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/t.jpg",
};

const NEW_ARRIVAL_SLUGS = [
  "flagship-moving-coil-phono-cartridge-dl-103r",
  "dilwale-vcf-2693-red-coloured-lp-record",
  "dual-moving-magnet-cartridge-at-vm95e",
  "pukar-1999-blue-coloured-lp",
];

const HOME_CATEGORY_RULES = [
  { pattern: /lp|vinyl|record/i, category: "lp-records" },
  { pattern: /blu-?ray|dvd|movie|box set|season/i, category: "blu-ray-movies" },
  { pattern: /tcl|led tv|qled|casio tv|inch.*tv/i, category: "led-tvs" },
  { pattern: /soundbar|sound bar|speaker|audio-expert|psb/i, category: "sound-bars" },
  { pattern: /purifier|dehumidifier/i, category: "air-purifiers" },
  { pattern: /theatre|theater|cinema|giga sound/i, category: "home-theater" },
  { pattern: /cartridge|denon|audio-technica|phono/i, category: "audio-equipment" },
  { pattern: /charger|usb|ldnio|mobile/i, category: "mobile-accessories" },
  { pattern: /tv stand|trolly|gecko|oz-fi/i, category: "tv-trolly-stand" },
  { pattern: /\b4k\b|uhd/i, category: "4k-moives" },
];

function decodeHtml(value) {
  return value
    .replace(/&amp;/g, "&")
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'");
}

function normalizeCategory(sourceCategory) {
  return CATEGORY_ALIASES[sourceCategory] || sourceCategory;
}

function resolveCategory(product, sourceCategory) {
  const normalizedSource = normalizeCategory(sourceCategory);
  if (normalizedSource !== "homepage" && CATEGORY_META[normalizedSource]) {
    return normalizedSource;
  }

  const text = `${product.name} ${product.slug}`;
  for (const rule of HOME_CATEGORY_RULES) {
    if (rule.pattern.test(text)) return rule.category;
  }

  return "accessories";
}

function normalizeProduct(product, sourceCategory) {
  const category = resolveCategory(product, sourceCategory);
  const meta = CATEGORY_META[category] ?? CATEGORY_META.accessories;
  const rating = Math.round((4.5 + (product.slug.length % 5) * 0.1) * 10) / 10;

  return {
    id: product.slug,
    name: decodeHtml(product.name),
    slug: product.slug,
    price: product.price,
    image: product.image,
    imageAlt: decodeHtml(product.imageAlt || product.name),
    category,
    subCategory: meta.subCategory,
    rating,
    reviewCount: 10 + (product.slug.length % 80),
    featured: NEW_ARRIVAL_SLUGS.includes(product.slug),
    badge: NEW_ARRIVAL_SLUGS.includes(product.slug) ? "NEW" : undefined,
  };
}

const productMap = new Map();

for (const [sourceCategory, products] of Object.entries(scraped.byCategory)) {
  for (const product of products) {
    const normalized = normalizeProduct(product, sourceCategory);
    const existing = productMap.get(normalized.slug);
    if (!existing || existing.category === "accessories") {
      productMap.set(normalized.slug, normalized);
    }
  }
}

const allProducts = [...productMap.values()].sort((a, b) =>
  a.name.localeCompare(b.name)
);

const categoryCounts = {};
for (const product of allProducts) {
  categoryCounts[product.category] = (categoryCounts[product.category] || 0) + 1;
}

const storeCategories = Object.keys(CATEGORY_META)
  .filter((slug) => categoryCounts[slug])
  .map((slug) => ({
    id: slug,
    name: CATEGORY_META[slug].name,
    slug,
    image: CATEGORY_IMAGES[slug] || CATEGORY_IMAGES.accessories,
    imageAlt: CATEGORY_META[slug].name,
    productCount: categoryCounts[slug] || 0,
  }))
  .sort((a, b) => a.name.localeCompare(b.name));

function byCategory(category, limit = 8) {
  return allProducts.filter((product) => product.category === category).slice(0, limit);
}

function pickSlugs(slugs) {
  return slugs
    .map((slug) => allProducts.find((product) => product.slug === slug))
    .filter(Boolean);
}

const newArrivalProducts = pickSlugs(NEW_ARRIVAL_SLUGS);
const bestSellingProducts = [
  ...byCategory("led-tvs", 2),
  ...byCategory("sound-bars", 1),
  ...byCategory("air-purifiers", 1),
].slice(0, 4);

const featuredProducts = [
  byCategory("led-tvs", 1)[0],
  byCategory("sound-bars", 1)[0],
  byCategory("blu-ray-movies", 1)[0],
  byCategory("lp-records", 1)[0],
].filter(Boolean);

const showcaseProduct =
  allProducts.find(
    (product) =>
      product.slug ===
      "samsung-hw-q900a-7-1-2ch-soundbar-with-dolby-atmos-dts-x-alexa-built-in"
  ) || byCategory("sound-bars", 1)[0];

const showcaseGallery = byCategory("sound-bars", 4).map((product) => product.image);

function serialize(value, indent = 0) {
  const pad = "  ".repeat(indent);
  if (value === undefined) return "undefined";
  if (value === null) return "null";
  if (typeof value === "string") return JSON.stringify(value);
  if (typeof value === "number" || typeof value === "boolean") return String(value);
  if (Array.isArray(value)) {
    if (value.length === 0) return "[]";
    return `[\n${value
      .map((item) => `${pad}  ${serialize(item, indent + 1)},`)
      .join("\n")}\n${pad}]`;
  }
  const entries = Object.entries(value).filter(([, v]) => v !== undefined);
  return `{\n${entries
    .map(([key, val]) => `${pad}  ${key}: ${serialize(val, indent + 1)},`)
    .join("\n")}\n${pad}}`;
}

const catalogTs = `import type { Category, Product } from "@/types";

export const allProducts: Product[] = ${serialize(allProducts, 0)};

export const storeCategories: Category[] = ${serialize(storeCategories, 0)};

export function getProductsByCategory(category: string, limit = 8): Product[] {
  return allProducts.filter((product) => product.category === category).slice(0, limit);
}

export const newArrivalProducts: Product[] = ${serialize(newArrivalProducts, 0)};

export const bestSellingProducts: Product[] = ${serialize(bestSellingProducts, 0)};

export const ledTvProducts: Product[] = ${serialize(byCategory("led-tvs", 8), 0)};

export const soundBarProducts: Product[] = ${serialize(byCategory("sound-bars", 8), 0)};

export const airPurifierProducts: Product[] = ${serialize(byCategory("air-purifiers", 8), 0)};

export const homeTheaterProducts: Product[] = ${serialize(byCategory("home-theater", 8), 0)};

export const featuredProducts: Product[] = ${serialize(featuredProducts, 0)};

export const showcaseProduct: Product = ${serialize(showcaseProduct, 0)};

export const showcaseGallery: string[] = ${serialize(showcaseGallery, 0)};

export const dealProducts: Product[] = ${serialize(
  [...byCategory("blu-ray-movies", 2), ...byCategory("4k-moives", 2)],
  0
)};
`;

fs.writeFileSync("src/data/catalog.ts", catalogTs);
console.log(`Wrote src/data/catalog.ts with ${allProducts.length} products`);
